<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Penyewaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenyewaanController extends Controller
{
    public function index(): View { return view('admin.penyewaan.index', ['penyewaan' => Penyewaan::with(['kamar.kos', 'penghuni'])->latest()->paginate(15)]); }
    public function create(): View { return view('admin.penyewaan.form', ['penyewaan' => new Penyewaan(), 'kamar' => Kamar::with('kos')->where('status', 'tersedia')->get(), 'penghuni' => Penghuni::where('status', 'aktif')->orderBy('nama')->get()]); }
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['kamar_id' => ['required', 'exists:kamar,id'], 'penghuni_id' => ['required', 'exists:penghuni,id'], 'tanggal_mulai' => ['required', 'date'], 'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai']]);
        $kamar = Kamar::findOrFail($data['kamar_id']);
        if ($kamar->status !== 'tersedia' || $kamar->penyewaan()->where('status', 'aktif')->exists()) return back()->withErrors(['kamar_id' => 'Kamar tidak tersedia.'])->withInput();
        DB::transaction(function () use ($data, $kamar) { Penyewaan::create(array_merge($data, ['status' => 'aktif'])); $kamar->update(['status' => 'terisi']); });
        return redirect()->route('admin.penyewaan.index')->with('success', 'Penyewaan berhasil dibuat.');
    }
    public function update(Request $request, Penyewaan $penyewaan): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:aktif,selesai,batal'], 'tanggal_akhir' => ['nullable', 'date']]);
        DB::transaction(function () use ($data, $penyewaan) { $penyewaan->update($data); if ($data['status'] !== 'aktif') $penyewaan->kamar->update(['status' => 'tersedia']); });
        return back()->with('success', 'Status penyewaan diperbarui.');
    }
}