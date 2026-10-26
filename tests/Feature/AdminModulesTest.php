<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Penghuni;
use App\Models\Penyewaan;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function room(): Kamar
    {
        $w = Wilayah::firstOrCreate(['nama' => 'Margonda']);
        $kos = Kos::create(['wilayah_id' => $w->id, 'nama' => 'Cabang Uji', 'slug' => 'cabang-'.uniqid(), 'alamat' => 'Alamat uji', 'status' => true]);

        return Kamar::create(['kos_id' => $kos->id, 'kode' => 'A1', 'harga' => 1000000, 'status' => 'tersedia']);
    }

    public function test_all_admin_pages_render_and_guest_and_tenant_are_denied(): void
    {
        $this->admin();
        $room = $this->room();
        $tenant = Penghuni::create(['nama' => 'Uji', 'telepon' => '08123', 'status' => 'aktif']);
        $facility = Fasilitas::create(['nama' => 'WiFi']);
        $paths = ['/admin', '/admin/kos', '/admin/kos/create', '/admin/kos/'.$room->kos_id, '/admin/kos/'.$room->kos_id.'/edit',
            '/admin/kamar', '/admin/kamar/create', '/admin/kamar/'.$room->id.'/edit', '/admin/wilayah', '/admin/wilayah/create', '/admin/wilayah/'.$room->kos->wilayah_id.'/edit',
            '/admin/fasilitas', '/admin/fasilitas/create', '/admin/fasilitas/'.$facility->id.'/edit', '/admin/penghuni', '/admin/penghuni/create', '/admin/penghuni/'.$tenant->id.'/edit',
            '/admin/penyewaan', '/admin/penyewaan/create', '/admin/tagihan', '/admin/tagihan/create', '/admin/verifikasi-pembayaran', '/admin/laporan'];
        foreach ($paths as $path) {
            $this->get($path)->assertOk()->assertSee('Menu owner');
        }
        $this->actingAs(User::factory()->create(['role' => 'penyewa']));
        foreach ($paths as $path) {
            $this->get($path)->assertForbidden();
        }
    }

    public function test_admin_filters_return_matching_rooms_and_rentals(): void
    {
        $this->admin();
        $room = $this->room();
        $this->get(route('admin.kamar.index', ['status' => 'tersedia', 'q' => 'A1', 'kos_id' => $room->kos_id]))->assertOk()->assertViewHas('kamar', fn ($v) => $v->total() === 1);
        $this->get(route('admin.kamar.index', ['status' => 'dipesan']))->assertOk()->assertViewHas('kamar', fn ($v) => $v->total() === 0);
        $this->get(route('admin.penyewaan.index', ['status' => 'aktif', 'q' => 'tidak ada']))->assertOk()->assertViewHas('penyewaan', fn ($v) => $v->total() === 0);
        $this->get(route('admin.tagihan.index', ['status' => 'lunas', 'q' => 'tidak ada']))->assertOk()->assertViewHas('tagihan', fn ($v) => $v->total() === 0);
    }

    public function test_location_and_facility_crud_and_protected_location_deletion(): void
    {
        $this->admin();
        $this->post(route('admin.wilayah.store'), ['nama' => 'Lokasi uji'])->assertRedirect();
        $w = Wilayah::firstOrFail();
        $this->put(route('admin.wilayah.update', $w), ['nama' => 'Lokasi baru'])->assertRedirect();
        $this->assertDatabaseHas('wilayah', ['nama' => 'Lokasi baru']);
        $this->delete(route('admin.wilayah.destroy', $w))->assertRedirect();
        $this->assertModelMissing($w);
        $room = $this->room();
        $this->delete(route('admin.wilayah.destroy', $room->kos->wilayah))->assertSessionHas('error');
        $this->post(route('admin.fasilitas.store'), ['nama' => 'WiFi'])->assertRedirect();
        $f = Fasilitas::firstOrFail();
        $this->post(route('admin.fasilitas.store'), ['nama' => 'WiFi'])->assertSessionHasErrors('nama');
        $this->put(route('admin.fasilitas.update', $f), ['nama' => 'AC'])->assertRedirect();
        $this->delete(route('admin.fasilitas.destroy', $f))->assertRedirect();
        $this->assertModelMissing($f);
    }

    public function test_branch_photos_facilities_and_delete_guards(): void
    {
        Storage::fake('public');
        $this->admin();
        $w = Wilayah::create(['nama' => 'Kalibata']);
        $f = Fasilitas::create(['nama' => 'WiFi']);
        $data = ['nama' => 'Kos Foto', 'slug' => 'kos-foto', 'wilayah_id' => $w->id, 'alamat' => 'Kalibata', 'status' => 1, 'fasilitas' => [$f->id]];
        $this->post(route('admin.kos.store'), $data + ['photos' => [UploadedFile::fake()->image('satu.jpg'), UploadedFile::fake()->image('dua.png')]])->assertRedirect();
        $kos = Kos::firstOrFail();
        $this->assertCount(2, $kos->fotoKos);
        $this->assertCount(1, $kos->fasilitas);
        $photo = $kos->fotoKos->last();
        Storage::disk('public')->assertExists($photo->path);
        $this->patch(route('admin.foto.primary', $photo))->assertRedirect();
        $this->assertSame(1, $kos->fotoKos()->where('is_primary', true)->count());
        $this->delete(route('admin.foto.destroy', $photo))->assertRedirect();
        Storage::disk('public')->assertMissing($photo->path);
        $this->assertSame(1, $kos->fotoKos()->where('is_primary', true)->count());
        $this->put(route('admin.kos.update', $kos), array_replace($data, ['nama' => 'Kos Diperbarui']))->assertRedirect();
        $this->post(route('admin.kos.store'), array_replace($data, ['slug' => 'invalid']) + ['photos' => [UploadedFile::fake()->create('script.php', 2, 'text/plain')]])->assertSessionHasErrors('photos.0');
        $this->delete(route('admin.kos.destroy', $kos))->assertRedirect();
        $this->assertModelMissing($kos);
    }

    public function test_room_and_tenant_crud_validation_and_history_protection(): void
    {
        $this->admin();
        $room = $this->room();
        $data = ['kos_id' => $room->kos_id, 'kode' => 'B1', 'harga' => 1200000, 'status' => 'tersedia'];
        $this->post(route('admin.kamar.store'), $data)->assertRedirect();
        $this->post(route('admin.kamar.store'), $data)->assertSessionHasErrors('kode');
        $new = Kamar::where('kode', 'B1')->firstOrFail();
        $this->put(route('admin.kamar.update', $new), array_replace($data, ['harga' => 1300000]))->assertRedirect();
        $this->delete(route('admin.kamar.destroy', $new))->assertRedirect();
        $this->assertModelMissing($new);
        $this->post(route('admin.penghuni.store'), ['nama' => 'Penghuni', 'telepon' => '08123', 'status' => 'aktif'])->assertRedirect();
        $tenant = Penghuni::firstOrFail();
        $this->put(route('admin.penghuni.update', $tenant), ['nama' => 'Nama baru', 'telepon' => '08124', 'status' => 'aktif'])->assertRedirect();
        $this->post(route('admin.penyewaan.store'), ['kamar_id' => $room->id, 'penghuni_id' => $tenant->id, 'tanggal_mulai' => today()->toDateString()])->assertRedirect();
        $this->delete(route('admin.penghuni.destroy', $tenant))->assertSessionHas('error');
        $this->delete(route('admin.kamar.destroy', $room))->assertSessionHas('error');
        $rental = Penyewaan::firstOrFail();
        $this->patch(route('admin.penyewaan.update', $rental), ['status' => 'selesai'])->assertRedirect();
        $this->assertSame('tersedia', $room->fresh()->status);
    }

    public function test_reports_filter_payments_by_branch_exclude_cancelled_and_export_csv(): void
    {
        $this->admin();
        $room = $this->room();
        $other = $this->room();
        foreach ([$room, $other] as $i => $r) {
            $tenant = Penghuni::create(['nama' => 'Penghuni '.$i, 'telepon' => '08123', 'status' => 'aktif']);
            $rental = Penyewaan::create(['kamar_id' => $r->id, 'penghuni_id' => $tenant->id, 'tanggal_mulai' => today(), 'status' => 'aktif']);
            $data = ['penyewaan_id' => $rental->id, 'nomor_referensi' => 'INV-'.$i, 'periode_mulai' => today()->toDateString(), 'periode_akhir' => today()->addMonth()->toDateString(), 'nominal' => 1000000, 'tanggal_terbit' => today()->toDateString(), 'tanggal_jatuh_tempo' => today()->addDay()->toDateString()];
            $this->post(route('admin.tagihan.store'), $data)->assertRedirect();
            $invoice = Tagihan::where('nomor_referensi', 'INV-'.$i)->firstOrFail();
            $this->post(route('admin.pembayaran.store', $invoice), ['nominal' => 400000 + $i * 100000, 'tanggal_bayar' => today()->toDateString(), 'metode' => 'Transfer'])->assertRedirect();
        }
        $this->get(route('admin.laporan.index', ['kos_id' => $room->kos_id]))->assertOk()->assertViewHas('diterima', fn ($v) => (float) $v === 400000.0)->assertViewHas('piutang', fn ($v) => (float) $v === 600000.0);
        $this->get(route('admin.laporan.csv', ['kos_id' => $room->kos_id]))->assertOk()->assertDownload('laporan-tagihan.csv');
        $this->get(route('admin.laporan.index', ['mulai' => 'bukan-tanggal']))->assertSessionHasErrors('mulai');
        $rental->update(['status' => 'batal']);
        $this->get(route('admin.laporan.index', ['kos_id' => $other->kos_id]))->assertViewHas('piutang', fn ($v) => (float) $v === 0.0);
    }

    public function test_public_filters_apply_price_range_to_the_same_room(): void
    {
        $room = $this->room();
        $room->update(['harga' => 500000]);
        Kamar::create(['kos_id' => $room->kos_id, 'kode' => 'A2', 'harga' => 2000000, 'status' => 'tersedia']);
        $this->get(route('public.kos.index', ['min_harga' => 900000, 'max_harga' => 1500000]))->assertOk()->assertViewHas('kos', fn ($v) => $v->total() === 0);
        $this->get(route('public.kos.index', ['max_harga' => 1000000]))->assertOk()->assertViewHas('kos', fn ($v) => $v->total() === 1);
        $facility = Fasilitas::create(['nama' => 'AC']);
        $this->get(route('public.kos.index', ['fasilitas_id' => $facility->id]))->assertOk()->assertViewHas('kos', fn ($v) => $v->total() === 0);
        $room->kos->fasilitas()->attach($facility);
        $this->get(route('public.kos.index', ['fasilitas_id' => $facility->id, 'q' => 'Cabang', 'wilayah_id' => $room->kos->wilayah_id, 'sort' => 'termahal']))->assertOk()->assertViewHas('kos', fn ($v) => $v->total() === 1);
        $this->get(route('public.kos.show', $room->kos))->assertOk()->assertSee('Sewa kamar');
        $room->kos->update(['status' => false]);
        $this->get(route('public.kos.show', $room->kos))->assertNotFound();
    }
}
