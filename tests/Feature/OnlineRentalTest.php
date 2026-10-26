<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Penyewaan;
use App\Models\User;
use App\Models\Wilayah;
use App\Services\OnlineRentalService;
use Database\Seeders\CabangKosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnlineRentalTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_account_switch_instead_of_booking_form_and_post_does_not_return_forbidden(): void
    {
        $room = $this->room();
        $owner = User::factory()->create(['role' => 'admin']);
        $this->actingAs($owner)->get(route('penyewa.create', $room))
            ->assertOk()->assertSee('Masuk dengan akun penyewa')->assertSee('Keluar dan ganti akun');
        $this->post(route('penyewa.store', $room), [
            'telepon' => '08123456789', 'tanggal_mulai' => today()->toDateString(), 'durasi' => 2,
        ])->assertRedirect(route('penyewa.create', $room))->assertSessionHas('error');
        $this->assertDatabaseCount('penyewaan', 0);
        $this->assertSame('tersedia', $room->fresh()->status);
        $tenant = User::factory()->create(['role' => 'penyewa']);
        $this->actingAs($tenant)->get(route('penyewa.create', $room))->assertOk()->assertSee('Pesan dan lanjut pembayaran');
        $this->book($tenant, $room);
        $this->get(route('penyewa.index'))->assertOk()->assertSee('Upload bukti pembayaran');
    }

    private function room(): Kamar
    {
        $wilayah = Wilayah::create(['nama' => 'Margonda']);
        $kos = Kos::create(['wilayah_id' => $wilayah->id, 'nama' => 'Kos Margonda', 'slug' => 'kos-margonda', 'alamat' => 'Margonda', 'status' => true]);

        return Kamar::create(['kos_id' => $kos->id, 'kode' => 'A1', 'harga' => 1500000, 'status' => 'tersedia']);
    }

    private function book(User $user, Kamar $room): Penyewaan
    {
        $this->actingAs($user)->post(route('penyewa.store', $room), [
            'telepon' => '08123456789', 'tanggal_mulai' => today()->toDateString(), 'durasi' => 2,
            'nominal' => 1,
        ])->assertRedirect(route('penyewa.index'));

        return Penyewaan::latest('id')->firstOrFail();
    }

    public function test_booking_is_automatic_price_is_from_database_and_duplicate_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'penyewa']);
        $room = $this->room();
        $rental = $this->book($user, $room);
        $this->assertSame('menunggu_pembayaran', $rental->status);
        $this->assertSame('3000000.00', $rental->tagihan->first()->nominal);
        $this->assertSame('dipesan', $room->fresh()->status);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->put(route('admin.kamar.update', $room), ['kos_id' => $room->kos_id, 'kode' => $room->kode, 'harga' => 1, 'status' => 'tersedia'])->assertSessionHasErrors('status');
        $this->post(route('admin.pembayaran.store', $rental->tagihan->first()), ['nominal' => 3000000, 'tanggal_bayar' => today()->toDateString(), 'metode' => 'Transfer'])->assertSessionHasErrors('pembayaran');
        $other = User::factory()->create(['role' => 'penyewa']);
        $this->actingAs($other)->post(route('penyewa.store', $room), [
            'telepon' => '08111111', 'tanggal_mulai' => today()->toDateString(), 'durasi' => 1,
        ])->assertSessionHasErrors('kamar_id');
        $this->assertSame(1, Penyewaan::count());
    }

    public function test_upload_is_private_not_income_until_owner_verifies_and_cannot_be_verified_twice(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'penyewa']);
        $room = $this->room();
        $rental = $this->book($user, $room);
        $invoice = $rental->tagihan->first();
        $this->post(route('penyewa.upload', $invoice), [
            'tanggal_bayar' => today()->toDateString(), 'bukti' => UploadedFile::fake()->image('transfer.jpg'),
        ])->assertRedirect();
        $payment = $invoice->pembayaran()->firstOrFail();
        $this->assertSame('menunggu_verifikasi', $rental->fresh()->status);
        $this->assertEquals(0, $invoice->totalPembayaranValid());
        Storage::disk('local')->assertExists($payment->bukti_path);
        $other = User::factory()->create(['role' => 'penyewa']);
        $this->actingAs($other)->get(route('pembayaran.bukti', $payment))->assertForbidden();
        $this->actingAs($other)->post(route('penyewa.upload', $invoice))->assertForbidden();
        $this->actingAs($other)->patch(route('admin.verifikasi.update', $payment), ['keputusan' => 'terima'])->assertForbidden();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.verifikasi.index'))->assertOk();
        $this->get(route('admin.verifikasi.show', $payment))->assertOk()->assertSee('Bukti transfer');
        $this->get(route('pembayaran.preview', $payment))->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->get(route('pembayaran.bukti', $payment))->assertDownload('bukti-pembayaran-'.$payment->id.'.jpg');
        $this->get(route('admin.tagihan.show', $invoice))->assertOk()->assertSee('Riwayat pembayaran');
        $this->patch(route('admin.verifikasi.update', $payment), ['keputusan' => 'terima'])->assertRedirect();
        $this->assertSame('lunas', $invoice->fresh()->status);
        $this->assertSame('aktif', $rental->fresh()->status);
        $this->assertSame('terisi', $room->fresh()->status);
        $this->patch(route('admin.verifikasi.update', $payment), ['keputusan' => 'terima'])->assertStatus(409);
        $this->actingAs($user)->get(route('penyewa.index'))->assertOk();
        $this->get(route('pembayaran.preview', $payment))->assertOk();
        $this->actingAs($other)->get(route('pembayaran.preview', $payment))->assertForbidden();
    }

    public function test_missing_proof_cannot_be_accepted_but_can_be_rejected_for_reupload(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'penyewa']);
        $rental = $this->book($user, $this->room());
        $invoice = $rental->tagihan->first();
        $this->post(route('penyewa.upload', $invoice), ['tanggal_bayar' => today()->toDateString(), 'bukti' => UploadedFile::fake()->image('bukti.png')])->assertRedirect();
        $payment = $invoice->pembayaran()->firstOrFail();
        Storage::disk('local')->delete($payment->bukti_path);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('admin.verifikasi.show', $payment))->assertOk()->assertSee('File bukti tidak tersedia');
        $this->get(route('pembayaran.preview', $payment))->assertNotFound();
        $this->patch(route('admin.verifikasi.update', $payment), ['keputusan' => 'terima'])->assertSessionHasErrors('bukti');
        $this->assertSame('menunggu_verifikasi', $payment->fresh()->status);
        $this->patch(route('admin.verifikasi.update', $payment), ['keputusan' => 'tolak', 'alasan' => 'File tidak tersedia, silakan upload ulang.'])->assertRedirect();
        $this->assertSame('menunggu_pembayaran', $rental->fresh()->status);
    }

    public function test_pdf_preview_and_payment_settings_are_protected_and_visible_to_tenant(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'penyewa']);
        $rental = $this->book($user, $this->room());
        $invoice = $rental->tagihan->first();
        $this->get(route('admin.rekening.edit'))->assertForbidden();
        $this->put(route('admin.rekening.update'), ['bank' => 'Uji', 'rekening' => '123', 'pemilik' => 'Uji'])->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('admin.rekening.edit'))->assertOk();
        $this->put(route('admin.rekening.update'), ['bank' => 'Bank Uji', 'rekening' => '123456789', 'pemilik' => 'Owner Uji'])->assertRedirect();
        $this->actingAs($user)->get(route('penyewa.index'))->assertOk()->assertSee('123456789')->assertSee('Owner Uji');
        Storage::disk('local')->put('bukti-pembayaran/uji.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF");
        $payment = $invoice->pembayaran()->create(['nominal' => $invoice->nominal, 'tanggal_bayar' => today(), 'metode' => 'Transfer', 'status' => 'menunggu_verifikasi', 'bukti_path' => 'bukti-pembayaran/uji.pdf']);
        $this->get(route('pembayaran.preview', $payment))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_rejection_allows_new_upload_and_expiry_releases_room(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'penyewa']);
        $room = $this->room();
        $rental = $this->book($user, $room);
        $invoice = $rental->tagihan->first();
        $this->post(route('penyewa.upload', $invoice), ['tanggal_bayar' => today()->toDateString(), 'bukti' => UploadedFile::fake()->image('bukti.png')])->assertRedirect();
        $payment = $invoice->pembayaran()->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->patch(route('admin.verifikasi.update', $payment), ['keputusan' => 'tolak', 'alasan' => 'Transfer belum diterima.'])->assertRedirect();
        $this->assertSame('ditolak', $payment->fresh()->status);
        $this->assertSame('menunggu_pembayaran', $rental->fresh()->status);
        $this->assertEquals(0, $invoice->totalPembayaranValid());
        $this->travel(25)->hours();
        app(OnlineRentalService::class)->expire();
        $this->assertSame('kedaluwarsa', $rental->fresh()->status);
        $this->assertSame('dibatalkan', $invoice->fresh()->status);
        $this->assertSame('tersedia', $room->fresh()->status);
    }

    public function test_invalid_upload_and_other_users_cancellation_are_rejected(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'penyewa']);
        $rental = $this->book($user, $this->room());
        $this->post(route('penyewa.upload', $rental->tagihan->first()), [
            'tanggal_bayar' => today()->toDateString(), 'bukti' => UploadedFile::fake()->create('malware.exe', 10, 'application/octet-stream'),
        ])->assertSessionHasErrors('bukti');
        $other = User::factory()->create(['role' => 'penyewa']);
        $this->actingAs($other)->patch(route('penyewa.cancel', $rental))->assertForbidden();
        $this->actingAs($user)->patch(route('penyewa.cancel', $rental))->assertRedirect();
        $this->assertSame('tersedia', $rental->kamar->fresh()->status);
    }

    public function test_registration_cannot_create_owner_and_home_has_three_branches(): void
    {
        $this->seed(CabangKosSeeder::class);
        $this->get('/')->assertOk()->assertSee('Jatiwaringin')->assertSee('Kalibata')->assertSee('Margonda');
        $this->post(route('register.store'), ['name' => 'Penyewa', 'email' => 'penyewa@example.com', 'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 'admin'])->assertRedirect(route('penyewa.index'));
        $this->assertSame('penyewa', User::where('email', 'penyewa@example.com')->firstOrFail()->role);
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_pending_proof_prevents_duplicate_upload_and_does_not_expire_during_review(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'penyewa']);
        $rental = $this->book($user, $this->room());
        $invoice = $rental->tagihan->first();
        $this->post(route('penyewa.upload', $invoice), ['tanggal_bayar' => today()->toDateString(), 'bukti' => UploadedFile::fake()->image('bukti.png')])->assertRedirect();
        $this->post(route('penyewa.upload', $invoice), ['tanggal_bayar' => today()->toDateString(), 'bukti' => UploadedFile::fake()->image('duplikat.png')])->assertSessionHasErrors('bukti');
        $this->assertCount(1, Storage::disk('local')->allFiles('bukti-pembayaran'));
        $this->travel(25)->hours();
        app(OnlineRentalService::class)->expire();
        $this->assertSame('menunggu_verifikasi', $rental->fresh()->status);
        $this->assertSame('dipesan', $rental->kamar->fresh()->status);
    }

    public function test_tenant_login_redirects_to_own_area_even_after_attempting_admin_and_logout_works(): void
    {
        $user = User::factory()->create(['email' => 'login-tenant@example.com', 'role' => 'penyewa', 'password' => 'password123']);
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password123'])->assertRedirect(route('penyewa.index'));
        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_local_account_command_is_idempotent_and_blocked_outside_local(): void
    {
        $this->artisan('kos:akun-lokal')->assertFailed();
        $this->app->instance('env', 'local');
        $this->artisan('kos:akun-lokal')->assertSuccessful();
        $this->assertSame(2, User::count());
        $hash = User::where('email', 'owner.local@sediakos.test')->firstOrFail()->password;
        $this->artisan('kos:akun-lokal')->assertSuccessful();
        $this->assertSame($hash, User::where('email', 'owner.local@sediakos.test')->firstOrFail()->password);
        $this->assertSame(2, User::count());
    }
}
