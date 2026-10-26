<?php

use App\Models\Kos;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('kos:nonaktifkan-demo', function () {
    $jumlah = Kos::whereIn('slug', [
        'kos-nyaman-jogja', 'paviliun-sore', 'kost-harmoni-bantul',
        'pondok-teduh-gejayan', 'kost-bunga-melati', 'rumah-kos-prawirotaman',
        'kost-putri-cempaka', 'griya-mahasiswa', 'kost-sejuk-kasihan', 'paviliun-kenari',
    ])->update(['status' => false]);
    $this->info($jumlah.' kos demo dinonaktifkan. Riwayat dan data tetap tersimpan.');
})->purpose('Sembunyikan kos contoh Jogja dari katalog tanpa menghapus data');
