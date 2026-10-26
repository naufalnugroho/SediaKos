<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\Fasilitas;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicKosController extends Controller
{
    public function index(Request $request): View
    {
        $query = Kos::query()->where('status', true)->with(['wilayah', 'fasilitas'])->withCount(['kamar as kamar_tersedia_count' => fn ($q) => $q->where('status', 'tersedia')]);
        $query->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%'.$request->string('q').'%'));
        $query->when($request->filled('wilayah_id'), fn ($q) => $q->where('wilayah_id', $request->integer('wilayah_id')));
        $query->when($request->filled('min_harga'), fn ($q) => $q->whereHas('kamar', fn ($k) => $k->where('harga', '>=', $request->input('min_harga'))));
        $query->when($request->filled('max_harga'), fn ($q) => $q->whereHas('kamar', fn ($k) => $k->where('harga', '<=', $request->input('max_harga'))));
        $query->when($request->filled('fasilitas_id'), fn ($q) => $q->whereHas('fasilitas', fn ($f) => $f->whereKey($request->integer('fasilitas_id'))));
        $query->when($request->boolean('tersedia'), fn ($q) => $q->whereHas('kamar', fn ($k) => $k->where('status', 'tersedia')));
        $query->when($request->input('sort') === 'termurah', fn ($q) => $q->withMin('kamar', 'harga')->orderBy('kamar_min_harga'));
        $kos = $query->latest()->paginate(12)->withQueryString();
        return view('public.kos.index_v2', ['kos' => $kos, 'wilayah' => Wilayah::orderBy('nama')->get(), 'fasilitas' => Fasilitas::orderBy('nama')->get()]);
    }

    public function wilayah(Wilayah $wilayah, Request $request): View
    {
        $request->merge(['wilayah_id' => $wilayah->id]);
        return $this->index($request);
    }

    public function show(Kos $kos): View
    {
        abort_unless($kos->status, 404);
        $kos->load(['wilayah', 'fasilitas', 'fotoKos' => fn ($q) => $q->orderBy('sort_order'), 'kamar' => fn ($q) => $q->where('status', 'tersedia')->orderBy('harga')]);
        return view('public.kos.show_v2', compact('kos'));
    }
}