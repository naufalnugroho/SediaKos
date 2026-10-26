<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private function filters(Request $request): array
    {
        $request->validate([
            'mulai' => ['nullable', 'date'], 'akhir' => ['nullable', 'date', 'after_or_equal:mulai'],
            'wilayah_id' => ['nullable', 'integer', 'exists:wilayah,id'], 'kos_id' => ['nullable', 'integer', 'exists:kos,id'],
        ]);
        $mulai = $request->date('mulai')?->startOfDay() ?? now()->startOfMonth();
        $akhir = $request->date('akhir')?->endOfDay() ?? now()->endOfMonth();
        $query = Tagihan::with(['penyewaan.penghuni', 'penyewaan.kamar.kos'])
            ->withSum(['pembayaran as diterima' => fn ($q) => $q->where('status', 'valid')], 'nominal')
            ->where('status', '!=', 'dibatalkan')->whereHas('penyewaan', fn ($q) => $q->whereNotIn('status', ['batal', 'kedaluwarsa']))
            ->whereBetween('tanggal_terbit', [$mulai, $akhir]);
        $query->when($request->filled('wilayah_id'), fn ($q) => $q->whereHas('penyewaan.kamar.kos', fn ($k) => $k->where('wilayah_id', $request->integer('wilayah_id'))));
        $query->when($request->filled('kos_id'), fn ($q) => $q->whereHas('penyewaan.kamar', fn ($k) => $k->where('kos_id', $request->integer('kos_id'))));

        return [$query, $mulai, $akhir];
    }

    public function index(Request $request)
    {
        [$base, $mulai, $akhir] = $this->filters($request);
        $payments = Pembayaran::where('status', 'valid')->whereBetween('tanggal_bayar', [$mulai, $akhir]);
        $payments->when($request->filled('wilayah_id'), fn ($q) => $q->whereHas('tagihan.penyewaan.kamar.kos', fn ($k) => $k->where('wilayah_id', $request->integer('wilayah_id'))));
        $payments->when($request->filled('kos_id'), fn ($q) => $q->whereHas('tagihan.penyewaan.kamar', fn ($k) => $k->where('kos_id', $request->integer('kos_id'))));
        $piutang = (clone $base)->get()->sum(fn ($item) => max(0, (float) $item->nominal - (float) $item->diterima));

        return view('admin.laporan.index', [
            'tagihan' => $base->latest()->paginate(15)->withQueryString(), 'diterima' => $payments->sum('nominal'),
            'piutang' => $piutang, 'mulai' => $mulai, 'akhir' => $akhir,
            'wilayah' => Wilayah::orderBy('nama')->get(), 'kos' => Kos::orderBy('nama')->get(),
        ]);
    }

    public function csv(Request $request)
    {
        [$query] = $this->filters($request);

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Referensi', 'Penghuni', 'Cabang', 'Nominal Tagihan', 'Status', 'Sisa']);
            foreach ($query->latest()->cursor() as $tagihan) {
                $safe = fn ($text) => preg_match('/^[=+@\-\t\r]/', $text) ? "'".$text : $text;
                fputcsv($handle, [$safe($tagihan->nomor_referensi), $safe($tagihan->penyewaan->penghuni->nama), $safe($tagihan->penyewaan->kamar->kos->nama), $tagihan->nominal, $tagihan->status, max(0, $tagihan->nominal - $tagihan->diterima)]);
            }
            fclose($handle);
        }, 'laporan-tagihan.csv', ['Content-Type' => 'text/csv']);
    }
}
