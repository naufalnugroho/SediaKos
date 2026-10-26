<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FasilitasController extends Controller
{
    public function index(): View
    {
        return view('admin.fasilitas.index_v2', ['fasilitas' => Fasilitas::withCount('kos')->orderBy('nama')->paginate(10)]);
    }

    public function create(): View { return view('admin.fasilitas.form', ['fasilitas' => new Fasilitas()]); }

    public function store(Request $request): RedirectResponse
    {
        Fasilitas::create($request->validate(['nama' => ['required', 'string', 'max:100', 'unique:fasilitas,nama']]));
        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Fasilitas $fasilitas): View { return view('admin.fasilitas.form', compact('fasilitas')); }

    public function update(Request $request, Fasilitas $fasilitas): RedirectResponse
    {
        $fasilitas->update($request->validate(['nama' => ['required', 'string', 'max:100', 'unique:fasilitas,nama,'.$fasilitas->id]]));
        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Fasilitas $fasilitas): RedirectResponse
    {
        $fasilitas->delete();
        return back()->with('success', 'Fasilitas berhasil dihapus.');
    }
}