<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Penyewaan;
use App\Services\OnlineRentalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PenyewaanController extends Controller
{
    public function index(Request $request): View
    {
        app(OnlineRentalService::class)->expire();
        $request->validate(['q' => ['nullable', 'string', 'max:150'], 'status' => ['nullable', 'in:aktif,selesai,batal,kedaluwarsa,menunggu_pembayaran,menunggu_verifikasi']]);
        $query = Penyewaan::with(['kamar.kos', 'penghuni', 'tagihan']);
        $query->when($request->filled('q'), fn ($q) => $q->whereHas('penghuni', fn ($p) => $p->where('nama', 'like', '%'.$request->input('q').'%')));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')));

        return view('admin.penyewaan.index', ['penyewaan' => $query->latest()->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        return view('admin.penyewaan.form', ['penyewaan' => new Penyewaan, 'kamar' => Kamar::with('kos')->where('status', 'tersedia')->get(), 'penghuni' => Penghuni::where('status', 'aktif')->orderBy('nama')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['kamar_id' => ['required', 'exists:kamar,id'], 'penghuni_id' => ['required', 'exists:penghuni,id'], 'tanggal_mulai' => ['required', 'date'], 'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai']]);
        DB::transaction(function () use ($data) {
            $kamar = Kamar::lockForUpdate()->findOrFail($data['kamar_id']);
            if ($kamar->status !== 'tersedia' || $kamar->penyewaan()->whereIn('status', ['aktif', 'menunggu_pembayaran', 'menunggu_verifikasi'])->exists()) {
                throw ValidationException::withMessages(['kamar_id' => 'Kamar tidak tersedia.']);
            }
            Penyewaan::create(array_merge($data, ['status' => 'aktif']));
            $kamar->update(['status' => 'terisi']);
        });

        return redirect()->route('admin.penyewaan.index')->with('success', 'Penyewaan berhasil dibuat.');
    }

    public function update(Request $request, Penyewaan $penyewaan): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:selesai'], 'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:'.$penyewaan->tanggal_mulai->toDateString()]]);
        DB::transaction(function () use ($data, $penyewaan) {
            $room = Kamar::lockForUpdate()->findOrFail($penyewaan->kamar_id);
            $penyewaan->refresh();
            abort_unless($penyewaan->status === 'aktif', 409);
            $penyewaan->update($data);
            if (! $penyewaan->penghuni->penyewaan()->where('status', 'aktif')->exists()) {
                $penyewaan->penghuni->update(['status' => 'nonaktif']);
            }
            if (! $room->penyewaan()->whereIn('status', ['aktif', 'menunggu_pembayaran', 'menunggu_verifikasi'])->exists()) {
                $room->update(['status' => 'tersedia']);
            }
        });

        return back()->with('success', 'Status penyewaan diperbarui.');
    }
}
