<?php

use App\Http\Controllers\Admin\FasilitasController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\KosController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PengaturanPembayaranController;
use App\Http\Controllers\Admin\PenghuniController;
use App\Http\Controllers\Admin\PenyewaanController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\VerifikasiPembayaranController;
use App\Http\Controllers\Admin\WilayahController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\PublicKosController;
use App\Http\Controllers\RegistrasiController;
use Illuminate\Support\Facades\Route;

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
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/daftar', [RegistrasiController::class, 'create'])->name('register');
    Route::post('/daftar', [RegistrasiController::class, 'store'])->middleware('throttle:10,1')->name('register.store');
});
Route::middleware('auth')->group(function () {
    Route::get('/sewa-saya', [PenyewaController::class, 'index'])->name('penyewa.index');
    Route::get('/kamar/{kamar}/sewa', [PenyewaController::class, 'create'])->name('penyewa.create');
    Route::post('/kamar/{kamar}/sewa', [PenyewaController::class, 'store'])->name('penyewa.store');
    Route::post('/tagihan/{tagihan}/bukti', [PenyewaController::class, 'upload'])->middleware('throttle:10,1')->name('penyewa.upload');
    Route::patch('/sewa/{penyewaan}/batal', [PenyewaController::class, 'cancel'])->name('penyewa.cancel');
    Route::get('/pembayaran/{pembayaran}/bukti', [PenyewaController::class, 'bukti'])->name('pembayaran.bukti');
    Route::get('/pembayaran/{pembayaran}/preview', [PenyewaController::class, 'preview'])->name('pembayaran.preview');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/pengaturan-pembayaran', [PengaturanPembayaranController::class, 'edit'])->name('rekening.edit');
    Route::put('/pengaturan-pembayaran', [PengaturanPembayaranController::class, 'update'])->name('rekening.update');
    Route::get('/verifikasi-pembayaran/{pembayaran}', [VerifikasiPembayaranController::class, 'show'])->name('verifikasi.show');
    Route::get('/verifikasi-pembayaran', [VerifikasiPembayaranController::class, 'index'])->name('verifikasi.index');
    Route::patch('/verifikasi-pembayaran/{pembayaran}', [VerifikasiPembayaranController::class, 'update'])->name('verifikasi.update');
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('wilayah', WilayahController::class)->except(['show']);
    Route::resource('fasilitas', FasilitasController::class)->parameters(['fasilitas' => 'fasilitas'])->except(['show']);
    Route::resource('kos', KosController::class)->parameters(['kos' => 'kos']);
    Route::resource('kamar', KamarController::class)->except(['show']);
    Route::resource('penghuni', PenghuniController::class)->except(['show']);
    Route::get('penyewaan', [PenyewaanController::class, 'index'])->name('penyewaan.index');
    Route::get('penyewaan/create', [PenyewaanController::class, 'create'])->name('penyewaan.create');
    Route::post('penyewaan', [PenyewaanController::class, 'store'])->name('penyewaan.store');
    Route::patch('penyewaan/{penyewaan}', [PenyewaanController::class, 'update'])->name('penyewaan.update');
    Route::resource('tagihan', TagihanController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('tagihan/{tagihan}/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('tagihan/{tagihan}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::patch('pembayaran/{pembayaran}/batal', [PembayaranController::class, 'cancel'])->name('pembayaran.cancel');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/csv', [LaporanController::class, 'csv'])->name('laporan.csv');
    Route::delete('foto/{fotoKos}', [KosController::class, 'destroyPhoto'])->name('foto.destroy');
    Route::patch('foto/{fotoKos}/utama', [KosController::class, 'primaryPhoto'])->name('foto.primary');
});
