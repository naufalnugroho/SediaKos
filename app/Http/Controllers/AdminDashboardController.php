<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Penghuni;
use App\Models\Tagihan;
use App\Models\Wilayah;
use App\Services\OnlineRentalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $request->validate(['tanggal_mulai' => ['nullable', 'date'], 'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai']]);
        app(OnlineRentalService::class)->expire();
        $tanggalMulai = $request->date('tanggal_mulai')?->startOfDay() ?? now()->startOfMonth();
        $tanggalAkhir = $request->date('tanggal_akhir')?->endOfDay() ?? now()->endOfMonth();

        return view('admin.dashboard', [
            'menungguVerifikasi' => Pembayaran::where('status', 'menunggu_verifikasi')->count(),
            'kamarDipesan' => Kamar::where('status', 'dipesan')->count(),
            'totalWilayah' => Wilayah::count(),
            'kosAktif' => Kos::where('status', true)->count(),
            'totalKamar' => Kamar::count(),
            'kamarTersedia' => Kamar::where('status', 'tersedia')->count(),
            'kamarTerisi' => Kamar::where('status', 'terisi')->count(),
            'penghuniAktif' => Penghuni::where('status', 'aktif')->count(),
            'tagihanBelumLunas' => Tagihan::whereIn('status', ['belum_dibayar', 'dibayar_sebagian', 'terlambat'])->count(),
            'tagihanTerlambat' => Tagihan::where('status', 'terlambat')->count(),
            'pembayaranPeriode' => Pembayaran::where('status', 'valid')->whereBetween('tanggal_bayar', [$tanggalMulai, $tanggalAkhir])->sum('nominal'),
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }
}
