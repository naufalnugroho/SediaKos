<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $wilayah = [
            'Bantul', 'Banguntapan', 'Depok', 'Gamping', 'Godean',
            'Kasihan', 'Kotagede', 'Kraton', 'Mantrijeron', 'Mlati',
            'Ngaglik', 'Sewon', 'Sleman', 'Umbulharjo', 'Wirobrajan',
        ];

        foreach ($wilayah as $nama) {
            Wilayah::updateOrCreate(['nama' => $nama]);
        }
    }
}