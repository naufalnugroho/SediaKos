<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Penghuni;
use App\Models\Penyewaan;
use App\Models\Tagihan;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $wilayah = collect(['Bantul', 'Sleman', 'Depok'])->mapWithKeys(function (string $nama) {
            return [$nama => Wilayah::firstOrCreate(['nama' => $nama])];
        });

        $fasilitas = collect(['WiFi', 'Kamar mandi dalam', 'Parkir motor', 'Dapur bersama', 'AC'])->mapWithKeys(function (string $nama) {
            return [$nama => Fasilitas::firstOrCreate(['nama' => $nama])];
        });

        $kosData = [
            ['nama' => 'Kos Nyaman Jogja', 'slug' => 'kos-nyaman-jogja', 'wilayah' => 'Depok', 'alamat' => 'Jl. Kaliurang KM 5, Sleman', 'kontak' => '081234567890', 'fasilitas' => ['WiFi', 'Kamar mandi dalam', 'Parkir motor', 'AC']],
            ['nama' => 'Paviliun Sore', 'slug' => 'paviliun-sore', 'wilayah' => 'Sleman', 'alamat' => 'Jl. Monjali No. 12, Sleman', 'kontak' => '081298765432', 'fasilitas' => ['WiFi', 'Dapur bersama', 'Parkir motor']],
            ['nama' => 'Kost Harmoni Bantul', 'slug' => 'kost-harmoni-bantul', 'wilayah' => 'Bantul', 'alamat' => 'Jl. Imogiri Barat, Bantul', 'kontak' => '082112223333', 'fasilitas' => ['WiFi', 'Kamar mandi dalam', 'Parkir motor']],
            ['nama' => 'Pondok Teduh Gejayan', 'slug' => 'pondok-teduh-gejayan', 'wilayah' => 'Depok', 'alamat' => 'Jl. Affandi No. 24, Depok', 'kontak' => '082233445501', 'fasilitas' => ['WiFi', 'Kamar mandi dalam', 'Dapur bersama']],
            ['nama' => 'Kost Bunga Melati', 'slug' => 'kost-bunga-melati', 'wilayah' => 'Sleman', 'alamat' => 'Jl. Damai No. 8, Sleman', 'kontak' => '082233445502', 'fasilitas' => ['WiFi', 'Parkir motor', 'AC']],
            ['nama' => 'Rumah Kos Prawirotaman', 'slug' => 'rumah-kos-prawirotaman', 'wilayah' => 'Bantul', 'alamat' => 'Jl. Parangtritis KM 3, Bantul', 'kontak' => '082233445503', 'fasilitas' => ['WiFi', 'Kamar mandi dalam', 'AC']],
            ['nama' => 'Kost Putri Cempaka', 'slug' => 'kost-putri-cempaka', 'wilayah' => 'Depok', 'alamat' => 'Jl. Seturan Raya No. 15, Depok', 'kontak' => '082233445504', 'fasilitas' => ['WiFi', 'Parkir motor', 'Dapur bersama']],
            ['nama' => 'Griya Mahasiswa', 'slug' => 'griya-mahasiswa', 'wilayah' => 'Sleman', 'alamat' => 'Jl. Babarsari No. 10, Sleman', 'kontak' => '082233445505', 'fasilitas' => ['WiFi', 'Kamar mandi dalam', 'Parkir motor', 'AC']],
            ['nama' => 'Kost Sejuk Kasihan', 'slug' => 'kost-sejuk-kasihan', 'wilayah' => 'Bantul', 'alamat' => 'Jl. Wates KM 4, Bantul', 'kontak' => '082233445506', 'fasilitas' => ['WiFi', 'Dapur bersama', 'Parkir motor']],
            ['nama' => 'Paviliun Kenari', 'slug' => 'paviliun-kenari', 'wilayah' => 'Depok', 'alamat' => 'Jl. Selokan Mataram No. 6, Depok', 'kontak' => '082233445507', 'fasilitas' => ['WiFi', 'Kamar mandi dalam', 'Parkir motor']],
        ];

        foreach ($kosData as $data) {
            $kos = Kos::updateOrCreate(['slug' => $data['slug']], [
                'wilayah_id' => $wilayah[$data['wilayah']]->id,
                'nama' => $data['nama'],
                'alamat' => $data['alamat'],
                'deskripsi' => 'Kos nyaman dan aman untuk mahasiswa maupun pekerja.',
                'aturan' => 'Tamu wajib lapor kepada pengelola.',
                'kontak' => $data['kontak'],
                'status' => true,
            ]);
            $kos->fasilitas()->sync(collect($data['fasilitas'])->map(fn ($nama) => $fasilitas[$nama]->id)->all());

            foreach ([['kode' => 'A-01', 'harga' => 850000, 'status' => 'tersedia'], ['kode' => 'A-02', 'harga' => 950000, 'status' => 'tersedia'], ['kode' => 'A-03', 'harga' => 1100000, 'status' => 'pemeliharaan']] as $room) {
                Kamar::updateOrCreate(['kos_id' => $kos->id, 'kode' => $room['kode']], $room + ['harga_berlaku_mulai' => now()->startOfMonth()->toDateString()]);
            }
        }

        $penghuni = Penghuni::updateOrCreate(['telepon' => '081355500001'], ['nama' => 'Andi Pratama', 'status' => 'aktif', 'catatan' => 'Data demo']);
        $kos = Kos::where('slug', 'kos-nyaman-jogja')->firstOrFail();
        $kamar = Kamar::where('kos_id', $kos->id)->where('kode', 'A-01')->firstOrFail();
        $kamar->update(['status' => 'terisi']);
        $penyewaan = Penyewaan::firstOrCreate(['kamar_id' => $kamar->id, 'penghuni_id' => $penghuni->id, 'status' => 'aktif'], ['tanggal_mulai' => now()->startOfMonth()->toDateString()]);
        $tagihan = Tagihan::updateOrCreate(['nomor_referensi' => 'DEMO-INV-001'], ['penyewaan_id' => $penyewaan->id, 'periode_mulai' => now()->startOfMonth()->toDateString(), 'periode_akhir' => now()->endOfMonth()->toDateString(), 'nominal' => 850000, 'tanggal_terbit' => now()->startOfMonth()->toDateString(), 'tanggal_jatuh_tempo' => now()->addDays(10)->toDateString(), 'status' => 'dibayar_sebagian']);
        if (! $tagihan->pembayaran()->where('referensi', 'DEMO-PAY-001')->exists()) {
            $tagihan->pembayaran()->create(['nominal' => 300000, 'tanggal_bayar' => now()->toDateString(), 'metode' => 'Transfer', 'referensi' => 'DEMO-PAY-001', 'catatan' => 'Pembayaran demo', 'status' => 'valid']);
        }
    }
}