<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\FotoKos;
use App\Models\Kos;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KosController extends Controller
{
    public function index(): View
    {
        return view('admin.kos.index_v2', [
            'kos' => Kos::with('wilayah')->withCount('kamar')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.kos.form', ['kos' => new Kos(), 'wilayah' => Wilayah::orderBy('nama')->get(), 'fasilitas' => Fasilitas::orderBy('nama')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $kos = Kos::create($this->validated($request));
        $kos->fasilitas()->sync($request->input('fasilitas', []));
        $this->storePhotos($request, $kos);
        return redirect()->route('admin.kos.show', $kos)->with('success', 'Kos berhasil ditambahkan.');
    }

    public function show(Kos $kos): View
    {
        $kos->load(['wilayah', 'fasilitas', 'fotoKos' => fn ($query) => $query->orderBy('sort_order')]);
        return view('admin.kos.show', compact('kos'));
    }

    public function edit(Kos $kos): View
    {
        $kos->load('fasilitas');
        return view('admin.kos.form', ['kos' => $kos, 'wilayah' => Wilayah::orderBy('nama')->get(), 'fasilitas' => Fasilitas::orderBy('nama')->get()]);
    }

    public function update(Request $request, Kos $kos): RedirectResponse
    {
        $kos->update($this->validated($request, $kos));
        $kos->fasilitas()->sync($request->input('fasilitas', []));
        $this->storePhotos($request, $kos);
        return redirect()->route('admin.kos.show', $kos)->with('success', 'Kos berhasil diperbarui.');
    }

    public function destroy(Kos $kos): RedirectResponse
    {
        if ($kos->kamar()->exists()) {
            return back()->with('error', 'Kos tidak dapat dihapus karena memiliki kamar.');
        }

        $paths = $kos->fotoKos()->pluck('path')->all();
        DB::transaction(function () use ($kos) { $kos->fasilitas()->detach(); $kos->delete(); });
        foreach ($paths as $path) { Storage::disk('public')->delete($path); }
        return redirect()->route('admin.kos.index')->with('success', 'Kos berhasil dihapus.');
    }

    public function destroyPhoto(FotoKos $fotoKos): RedirectResponse
    {
        $path = $fotoKos->path;
        $fotoKos->delete();
        Storage::disk('public')->delete($path);
        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function primaryPhoto(FotoKos $fotoKos): RedirectResponse
    {
        DB::transaction(function () use ($fotoKos) {
            FotoKos::where('kos_id', $fotoKos->kos_id)->update(['is_primary' => false]);
            $fotoKos->update(['is_primary' => true]);
        });
        return back()->with('success', 'Foto utama berhasil diubah.');
    }

    private function validated(Request $request, ?Kos $kos = null): array
    {
        return $request->validate([
            'wilayah_id' => ['required', 'exists:wilayah,id'], 'nama' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash', 'unique:kos,slug'.($kos ? ','.$kos->id : '')],
            'alamat' => ['required', 'string'], 'deskripsi' => ['nullable', 'string'], 'aturan' => ['nullable', 'string'],
            'kontak' => ['nullable', 'string', 'max:50'], 'status' => ['boolean'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'fasilitas' => ['nullable', 'array'],
            'fasilitas.*' => ['integer', 'exists:fasilitas,id'],
        ]);
    }

    private function storePhotos(Request $request, Kos $kos): void
    {
        if (! $request->hasFile('photos')) return;
        $isFirst = ! $kos->fotoKos()->exists();
        foreach ($request->file('photos') as $index => $photo) {
            $path = $photo->store('kos/'.$kos->id, 'public');
            $kos->fotoKos()->create([
                'path' => $path, 'original_name' => $photo->getClientOriginalName(), 'mime_type' => $photo->getMimeType(),
                'size' => $photo->getSize(), 'is_primary' => $isFirst && $index === 0,
                'sort_order' => $kos->fotoKos()->max('sort_order') + $index + 1,
            ]);
        }
    }
}