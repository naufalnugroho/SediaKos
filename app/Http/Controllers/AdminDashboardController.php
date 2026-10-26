<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Penghuni;
use App\Models\Tagihan;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tanggalMulai = $request->date('tanggal_mulai')?->startOfDay() ?? now()->startOfMonth();
        $tanggalAkhir = $request->date('tanggal_akhir')?->endOfDay() ?? now()->endOfMonth();

        return view('admin.dashboard', [
            'totalWilayah' => Wilayah::count(),
            'kosAktif' => Kos::where('status', true)->count(),
            'totalKamar' => Kamar::count(),
            'kamarTersedia' => Kamar::where('status', 'tersedia')->count(),
            'kamarTerisi' => Kamar::where('status', 'terisi')->count(),
            'penghuniAktif' => Penghuni::where('status', 'aktif')->count(),
            'tagihanBelumLunas' => Tagihan::whereIn('status', ['belum_dibayar', 'dibayar_sebagian', 'terlambat'])->count(),
            'tagihanTerlambat' => Tagihan::where('status', 'terlambat')->count(),
            'pembayaranPeriode' => \App\Models\Pembayaran::where('status', 'valid')->whereBetween('tanggal_bayar', [$tanggalMulai, $tanggalAkhir])->sum('nominal'),
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }
}