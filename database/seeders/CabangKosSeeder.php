<?php

namespace Database\Seeders;

use App\Models\Kos;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CabangKosSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Jatiwaringin', 'Kalibata', 'Margonda'] as $nama) {
            $wilayah = Wilayah::firstOrCreate(['nama' => $nama]);
            Kos::firstOrCreate(['slug' => 'kos-'.Str::slug($nama)], [
                'wilayah_id' => $wilayah->id, 'nama' => 'Kos '.$nama,
                'alamat' => $nama, 'status' => true,
                'deskripsi' => 'Informasi alamat lengkap, fasilitas, foto, dan harga kamar akan diperbarui oleh pengelola.',
            ]);
        }
    }
}
