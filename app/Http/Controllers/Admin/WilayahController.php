<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WilayahController extends Controller
{
    public function index(): View
    {
        return view('admin.wilayah.index_v2', ['wilayah' => Wilayah::withCount('kos')->latest()->paginate(10)]);
    }

    public function create(): View
    {
        return view('admin.wilayah.form', ['wilayah' => new Wilayah()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:100', 'unique:wilayah,nama']]);
        Wilayah::create($data);

        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function edit(Wilayah $wilayah): View
    {
        return view('admin.wilayah.form', compact('wilayah'));
    }

    public function update(Request $request, Wilayah $wilayah): RedirectResponse
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:100', 'unique:wilayah,nama,'.$wilayah->id]]);
        $wilayah->update($data);

        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Wilayah $wilayah): RedirectResponse
    {
        if ($wilayah->kos()->exists()) {
            return back()->with('error', 'Wilayah tidak dapat dihapus karena masih memiliki kos.');
        }

        $wilayah->delete();
        return back()->with('success', 'Wilayah berhasil dihapus.');
    }
}