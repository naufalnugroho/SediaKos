<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penghuni;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenghuniController extends Controller
{
    public function index(): View
    {
        return view('admin.penghuni.index', ['penghuni' => Penghuni::withCount('penyewaan')->latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.penghuni.form', ['penghuni' => new Penghuni]);
    }

    public function store(Request $request): RedirectResponse
    {
        Penghuni::create($this->validated($request));

        return redirect()->route('admin.penghuni.index')->with('success', 'Penghuni berhasil ditambahkan.');
    }

    public function edit(Penghuni $penghuni): View
    {
        return view('admin.penghuni.form', compact('penghuni'));
    }

    public function update(Request $request, Penghuni $penghuni): RedirectResponse
    {
        $data = $this->validated($request);
        if ($data['status'] !== 'aktif' && $penghuni->penyewaan()->where('status', 'aktif')->exists()) {
            return back()->withErrors(['status' => 'Penghuni masih memiliki sewa aktif. Selesaikan penyewaan sebelum menonaktifkan penghuni.'])->withInput();
        }
        $penghuni->update($data);

        return redirect()->route('admin.penghuni.index')->with('success', 'Penghuni berhasil diperbarui.');
    }

    public function destroy(Penghuni $penghuni): RedirectResponse
    {
        if ($penghuni->penyewaan()->exists()) {
            return back()->with('error', 'Penghuni memiliki riwayat penyewaan.');
        } $penghuni->delete();

        return back()->with('success', 'Penghuni berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['nama' => ['required', 'string', 'max:150'], 'telepon' => ['required', 'string', 'max:30'], 'catatan' => ['nullable', 'string'], 'status' => ['required', 'in:aktif,nonaktif,calon']]);
    }
}
