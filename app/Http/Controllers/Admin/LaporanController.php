<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Kos;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $mulai = $request->date('mulai')?->startOfDay() ?? now()->startOfMonth();
        $akhir = $request->date('akhir')?->endOfDay() ?? now()->endOfMonth();
        $base = Tagihan::with('penyewaan.penghuni')->whereBetween('tanggal_terbit', [$mulai, $akhir]);
        $base->when($request->filled('wilayah_id'), fn ($q) => $q->whereHas('penyewaan.kamar.kos', fn ($k) => $k->where('wilayah_id', $request->integer('wilayah_id'))));
        $base->when($request->filled('kos_id'), fn ($q) => $q->whereHas('penyewaan.kamar', fn ($k) => $k->where('kos_id', $request->integer('kos_id'))));
        $semuaTagihan = (clone $base)->latest()->get();
        $tagihan = $base->latest()->paginate(15)->withQueryString();
        $diterima = Pembayaran::where('status', 'valid')->whereBetween('tanggal_bayar', [$mulai, $akhir])->sum('nominal');
        $piutang = $semuaTagihan->sum(fn ($item) => (float) $item->sisaNominal());

        return view('admin.laporan.index', ['tagihan' => $tagihan, 'diterima' => $diterima, 'piutang' => $piutang, 'mulai' => $mulai, 'akhir' => $akhir, 'wilayah' => Wilayah::orderBy('nama')->get(), 'kos' => Kos::orderBy('nama')->get()]);
    }

    public function csv(Request $request)
    {
        $mulai = $request->date('mulai')?->startOfDay() ?? now()->startOfMonth();
        $akhir = $request->date('akhir')?->endOfDay() ?? now()->endOfMonth();
        $query = Tagihan::with('penyewaan.penghuni')->whereBetween('tanggal_terbit', [$mulai, $akhir]);
        $query->when($request->filled('wilayah_id'), fn ($q) => $q->whereHas('penyewaan.kamar.kos', fn ($k) => $k->where('wilayah_id', $request->integer('wilayah_id'))));
        $query->when($request->filled('kos_id'), fn ($q) => $q->whereHas('penyewaan.kamar', fn ($k) => $k->where('kos_id', $request->integer('kos_id'))));
        $rows = $query->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Referensi', 'Penghuni', 'Nominal Tagihan', 'Status', 'Sisa']);
            foreach ($rows as $tagihan) fputcsv($handle, [$tagihan->nomor_referensi, $tagihan->penyewaan->penghuni->nama, $tagihan->nominal, $tagihan->status, $tagihan->sisaNominal()]);
            fclose($handle);
        }, 'laporan-tagihan.csv', ['Content-Type' => 'text/csv']);
    }
}