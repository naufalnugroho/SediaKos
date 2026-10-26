<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\FasilitasController;
use App\Http\Controllers\Admin\KosController;
use App\Http\Controllers\Admin\WilayahController;
use App\Http\Controllers\PublicKosController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\PenghuniController;
use App\Http\Controllers\Admin\PenyewaanController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/home', HomeController::class)->name('home.alias');
Route::get('/kos', [PublicKosController::class, 'index'])->name('public.kos.index');
Route::get('/wilayah/{wilayah}', [PublicKosController::class, 'wilayah'])->name('public.wilayah.show');
Route::get('/kos/{kos}', [PublicKosController::class, 'show'])->name('public.kos.show');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('wilayah', WilayahController::class)->except(['show']);
    Route::resource('fasilitas', FasilitasController::class)->except(['show']);
    Route::resource('kos', KosController::class);
    Route::resource('kamar', KamarController::class)->except(['show']);
    Route::resource('penghuni', PenghuniController::class)->except(['show']);
    Route::get('penyewaan', [PenyewaanController::class, 'index'])->name('penyewaan.index');
    Route::get('penyewaan/create', [PenyewaanController::class, 'create'])->name('penyewaan.create');
    Route::post('penyewaan', [PenyewaanController::class, 'store'])->name('penyewaan.store');
    Route::patch('penyewaan/{penyewaan}', [PenyewaanController::class, 'update'])->name('penyewaan.update');
    Route::resource('tagihan', TagihanController::class)->only(['index', 'create', 'store']);
    Route::get('tagihan/{tagihan}/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('tagihan/{tagihan}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::patch('pembayaran/{pembayaran}/batal', [PembayaranController::class, 'cancel'])->name('pembayaran.cancel');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/csv', [LaporanController::class, 'csv'])->name('laporan.csv');
    Route::delete('foto/{fotoKos}', [KosController::class, 'destroyPhoto'])->name('foto.destroy');
    Route::patch('foto/{fotoKos}/utama', [KosController::class, 'primaryPhoto'])->name('foto.primary');
});
