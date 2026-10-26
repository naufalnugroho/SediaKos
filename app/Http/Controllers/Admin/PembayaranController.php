<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function create(Tagihan $tagihan): View
    {
        abort_if($tagihan->penyewaan->user_id !== null, 403, 'Gunakan menu verifikasi pembayaran untuk pesanan online.');

        return view('admin.pembayaran.form', compact('tagihan'));
    }

    public function store(Request $request, Tagihan $tagihan): RedirectResponse
    {
        if ($tagihan->penyewaan->user_id) {
            return back()->withErrors(['pembayaran' => 'Untuk pesanan online, gunakan menu verifikasi bukti pembayaran.']);
        }
        $data = $request->validate(['nominal' => ['required', 'numeric', 'min:0.01'], 'tanggal_bayar' => ['required', 'date'], 'metode' => ['required', 'string', 'max:50'], 'referensi' => ['nullable', 'string', 'max:100'], 'catatan' => ['nullable', 'string']]);
        DB::transaction(function () use ($data, $tagihan, $request) {
            $tagihan = Tagihan::lockForUpdate()->findOrFail($tagihan->id);
            if ($tagihan->status === 'dibatalkan' || (float) $data['nominal'] > (float) $tagihan->sisaNominal()) {
                throw ValidationException::withMessages(['nominal' => 'Nominal pembayaran melebihi sisa tagihan atau tagihan sudah dibatalkan.']);
            }
            $tagihan->pembayaran()->create(array_merge($data, ['dicatat_oleh' => $request->user()->id, 'status' => 'valid', 'diverifikasi_pada' => now()]));
            $this->refreshStatus($tagihan->fresh());
        });

        return redirect()->route('admin.tagihan.index')->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function cancel(Pembayaran $pembayaran): RedirectResponse
    {
        if ($pembayaran->tagihan->penyewaan->user_id) {
            return back()->withErrors(['pembayaran' => 'Pembayaran sewa online yang sudah valid memerlukan proses koreksi/refund oleh pengelola; tidak dapat dibatalkan melalui pencatatan manual.']);
        }
        DB::transaction(function () use ($pembayaran) {
            Tagihan::lockForUpdate()->findOrFail($pembayaran->tagihan_id);
            $pembayaran->refresh();
            $pembayaran->update(['status' => 'dibatalkan']);
            $this->refreshStatus($pembayaran->tagihan->fresh());
        });

        return back()->with('success', 'Pembayaran dibatalkan dan tidak dihitung sebagai penerimaan.');
    }

    private function refreshStatus(Tagihan $tagihan): void
    {
        $total = (float) $tagihan->totalPembayaranValid();
        $status = $total >= (float) $tagihan->nominal ? 'lunas' : ($total > 0 ? 'dibayar_sebagian' : (now()->startOfDay()->gt($tagihan->tanggal_jatuh_tempo) ? 'terlambat' : 'belum_dibayar'));
        $tagihan->update(['status' => $status]);
    }
}
