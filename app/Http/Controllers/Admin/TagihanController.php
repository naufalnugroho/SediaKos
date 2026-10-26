<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penyewaan;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:150'], 'status' => ['nullable', 'in:belum_dibayar,dibayar_sebagian,lunas,terlambat,dibatalkan']]);
        $query = Tagihan::with(['penyewaan.penghuni', 'penyewaan.kamar.kos']);
        $query->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('nomor_referensi', 'like', '%'.$request->input('q').'%')->orWhereHas('penyewaan.penghuni', fn ($p) => $p->where('nama', 'like', '%'.$request->input('q').'%'))));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')));

        return view('admin.tagihan.index', ['tagihan' => $query->latest()->paginate(15)->withQueryString()]);
    }

    public function show(Tagihan $tagihan): View
    {
        $tagihan->load(['penyewaan.penghuni', 'penyewaan.kamar.kos', 'pembayaran' => fn ($q) => $q->latest(), 'pembayaran.adminPencatat']);

        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function create(): View
    {
        return view('admin.tagihan.form', ['tagihan' => new Tagihan, 'penyewaan' => Penyewaan::with(['penghuni', 'kamar.kos'])->whereNull('user_id')->where('status', 'aktif')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['penyewaan_id' => ['required', 'exists:penyewaan,id'], 'nomor_referensi' => ['required', 'string', 'max:100', 'unique:tagihan,nomor_referensi'], 'periode_mulai' => ['required', 'date'], 'periode_akhir' => ['required', 'date', 'after_or_equal:periode_mulai'], 'nominal' => ['required', 'numeric', 'min:0.01'], 'tanggal_terbit' => ['required', 'date'], 'tanggal_jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal_terbit']]);
        $penyewaan = Penyewaan::findOrFail($data['penyewaan_id']);
        if ($penyewaan->status !== 'aktif' || $penyewaan->user_id) {
            return back()->withErrors(['penyewaan_id' => 'Tagihan manual hanya untuk penyewaan manual yang aktif. Tagihan pesanan online dibuat otomatis.'])->withInput();
        }
        Tagihan::create(array_merge($data, ['status' => 'belum_dibayar']));

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dibuat.');
    }
}
