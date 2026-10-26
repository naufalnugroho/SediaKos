<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Penghuni;
use App\Models\Penyewaan;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RentalAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function rental(): array
    {
        $wilayah = Wilayah::create(['nama' => 'Wilayah Uji', 'slug' => 'wilayah-uji']);
        $kos = Kos::create(['wilayah_id' => $wilayah->id, 'nama' => 'Kos Uji', 'slug' => 'kos-uji', 'alamat' => 'Alamat Uji', 'status' => true]);
        $kamar = Kamar::create(['kos_id' => $kos->id, 'kode' => 'A-01', 'harga' => 1000000, 'status' => 'tersedia']);
        $penghuni = Penghuni::create(['nama' => 'Penghuni Uji', 'telepon' => '0800000000', 'status' => 'aktif']);

        return compact('wilayah', 'kos', 'kamar', 'penghuni');
    }

    public function test_active_rental_fills_room_and_duplicate_is_rejected(): void
    {
        $data = $this->rental();
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.penyewaan.store'), [
            'kamar_id' => $data['kamar']->id,
            'penghuni_id' => $data['penghuni']->id,
            'tanggal_mulai' => '2026-01-01',
        ]);

        $response->assertRedirect(route('admin.penyewaan.index'));
        $this->assertDatabaseHas('penyewaan', ['kamar_id' => $data['kamar']->id, 'status' => 'aktif']);
        $this->assertDatabaseHas('kamar', ['id' => $data['kamar']->id, 'status' => 'terisi']);

        $response = $this->actingAs($admin)->post(route('admin.penyewaan.store'), [
            'kamar_id' => $data['kamar']->id,
            'penghuni_id' => $data['penghuni']->id,
            'tanggal_mulai' => '2026-02-01',
        ]);

        $response->assertSessionHasErrors('kamar_id');
        $this->assertSame(1, Penyewaan::count());
    }

    public function test_partial_full_and_cancelled_payments_update_invoice_status(): void
    {
        $data = $this->rental();
        $admin = $this->admin();
        $rental = Penyewaan::create(['kamar_id' => $data['kamar']->id, 'penghuni_id' => $data['penghuni']->id, 'tanggal_mulai' => '2026-01-01', 'status' => 'aktif']);
        $tagihan = Tagihan::create(['penyewaan_id' => $rental->id, 'nomor_referensi' => 'INV-UJI-001', 'periode_mulai' => '2026-01-01', 'periode_akhir' => '2026-01-31', 'nominal' => 1000000, 'tanggal_terbit' => '2026-01-01', 'tanggal_jatuh_tempo' => '2026-01-10', 'status' => 'belum_dibayar']);

        $this->actingAs($admin)->post(route('admin.pembayaran.store', $tagihan), ['nominal' => 400000, 'tanggal_bayar' => '2026-01-02', 'metode' => 'Tunai'])->assertRedirect(route('admin.tagihan.index'));
        $this->assertDatabaseHas('tagihan', ['id' => $tagihan->id, 'status' => 'dibayar_sebagian']);

        $this->actingAs($admin)->post(route('admin.pembayaran.store', $tagihan), ['nominal' => 600000, 'tanggal_bayar' => '2026-01-03', 'metode' => 'Transfer'])->assertRedirect(route('admin.tagihan.index'));
        $this->assertDatabaseHas('tagihan', ['id' => $tagihan->id, 'status' => 'lunas']);

        $payment = $tagihan->pembayaran()->first();
        $this->actingAs($admin)->patch(route('admin.pembayaran.cancel', $payment))->assertRedirect();
        $this->assertDatabaseHas('pembayaran', ['id' => $payment->id, 'status' => 'dibatalkan']);
        $this->assertDatabaseHas('tagihan', ['id' => $tagihan->id, 'status' => 'dibayar_sebagian']);

        $response = $this->actingAs($admin)->post(route('admin.pembayaran.store', $tagihan), ['nominal' => 700000, 'tanggal_bayar' => '2026-01-04', 'metode' => 'Tunai']);
        $response->assertSessionHasErrors('nominal');
    }
}