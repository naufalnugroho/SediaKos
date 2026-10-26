<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KamarController extends Controller
{
    public function index(): View
    {
        return view('admin.kamar.index', ['kamar' => Kamar::with('kos')->latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.kamar.form', ['kamar' => new Kamar(), 'kos' => Kos::orderBy('nama')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Kamar::create($this->validated($request));
        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar): View
    {
        return view('admin.kamar.form', ['kamar' => $kamar, 'kos' => Kos::orderBy('nama')->get()]);
    }

    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        $kamar->update($this->validated($request, $kamar));
        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar): RedirectResponse
    {
        if ($kamar->penyewaan()->exists()) return back()->with('error', 'Kamar yang memiliki riwayat penyewaan tidak dapat dihapus.');
        $kamar->delete();
        return back()->with('success', 'Kamar berhasil dihapus.');
    }

    private function validated(Request $request, ?Kamar $kamar = null): array
    {
        return $request->validate([
            'kos_id' => ['required', 'exists:kos,id'], 'kode' => ['required', 'string', 'max:50'],
            'harga' => ['required', 'numeric', 'min:0'], 'harga_berlaku_mulai' => ['nullable', 'date'],
            'harga_berlaku_sampai' => ['nullable', 'date', 'after_or_equal:harga_berlaku_mulai'],
            'status' => ['required', 'in:tersedia,terisi,pemeliharaan'],
        ]);
    }
}