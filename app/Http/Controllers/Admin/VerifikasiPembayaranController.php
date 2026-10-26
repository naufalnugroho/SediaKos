<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VerifikasiPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['status' => ['nullable', 'in:menunggu_verifikasi,valid,ditolak'], 'kos_id' => ['nullable', 'integer', 'exists:kos,id']]);
        $query = Pembayaran::whereNotNull('bukti_path')->with('tagihan.penyewaan.penghuni', 'tagihan.penyewaan.kamar.kos');
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')));
        $query->when($request->filled('kos_id'), fn ($q) => $q->whereHas('tagihan.penyewaan.kamar', fn ($k) => $k->where('kos_id', $request->integer('kos_id'))));

        return view('admin.pembayaran.verifikasi', [
            'pembayaran' => $query->orderByRaw("CASE WHEN status = 'menunggu_verifikasi' THEN 0 ELSE 1 END")->latest()->paginate(15)->withQueryString(),
            'kos' => Kos::orderBy('nama')->get(),
            'menunggu' => Pembayaran::whereNotNull('bukti_path')->where('status', 'menunggu_verifikasi')->count(),
        ]);
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['tagihan.penyewaan.penghuni', 'tagihan.penyewaan.kamar.kos', 'adminPencatat']);
        $fileAda = $pembayaran->bukti_path && Storage::disk('local')->exists($pembayaran->bukti_path);
        $mime = $fileAda ? Storage::disk('local')->mimeType($pembayaran->bukti_path) : null;

        return view('admin.pembayaran.detail', compact('pembayaran', 'fileAda', 'mime'));
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $data = $request->validate([
            'keputusan' => ['required', 'in:terima,tolak'],
            'alasan' => ['required_if:keputusan,tolak', 'nullable', 'string', 'max:1000'],
        ]);
        DB::transaction(function () use ($pembayaran, $data, $request) {
            $rental = $pembayaran->tagihan->penyewaan;
            $room = Kamar::lockForUpdate()->findOrFail($rental->kamar_id);
            $tagihan = Tagihan::lockForUpdate()->findOrFail($pembayaran->tagihan_id);
            $pembayaran->refresh();
            $rental->refresh();
            abort_unless($pembayaran->status === 'menunggu_verifikasi' && $rental->status === 'menunggu_verifikasi', 409);
            if ($data['keputusan'] === 'terima' && (! $pembayaran->bukti_path || ! Storage::disk('local')->exists($pembayaran->bukti_path))) {
                throw ValidationException::withMessages(['bukti' => 'File bukti tidak ditemukan. Tolak bukti dan minta penyewa mengunggah ulang.']);
            }
            if ($data['keputusan'] === 'tolak') {
                $pembayaran->update(['status' => 'ditolak', 'alasan_penolakan' => $data['alasan'], 'dicatat_oleh' => $request->user()->id, 'diverifikasi_pada' => now()]);
                $rental->update(['status' => 'menunggu_pembayaran', 'batas_bayar' => now()->addDay()]);

                return;
            }
            if ($pembayaran->nominal > $tagihan->sisaNominal()) {
                throw ValidationException::withMessages(['pembayaran' => 'Nominal melebihi sisa tagihan. Periksa pembayaran lain terlebih dahulu.']);
            }
            $pembayaran->update(['status' => 'valid', 'dicatat_oleh' => $request->user()->id, 'diverifikasi_pada' => now()]);
            $tagihan->update(['status' => $tagihan->sisaNominal() <= 0 ? 'lunas' : 'dibayar_sebagian']);
            if ($tagihan->status === 'lunas') {
                $rental->update(['status' => 'aktif']);
                $rental->penghuni->update(['status' => 'aktif']);
                $room->update(['status' => 'terisi']);
            }
        });

        return back()->with('success', 'Hasil verifikasi pembayaran disimpan.');
    }
}
