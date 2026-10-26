<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Kos;
use App\Models\Wilayah;
use App\Services\OnlineRentalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicKosController extends Controller
{
    public function index(Request $request): View
    {
        app(OnlineRentalService::class)->expire();
        $request->validate([
            'q' => ['nullable', 'string', 'max:150'], 'wilayah_id' => ['nullable', 'integer', 'exists:wilayah,id'],
            'fasilitas_id' => ['nullable', 'integer', 'exists:fasilitas,id'],
            'min_harga' => ['nullable', 'numeric', 'min:0'], 'max_harga' => array_merge(['nullable', 'numeric', 'min:0'], $request->filled('min_harga') ? ['gte:min_harga'] : []),
            'sort' => ['nullable', 'in:termurah,termahal'],
        ]);
        $query = Kos::query()->where('status', true)->with(['wilayah', 'fasilitas', 'fotoKos' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')])->withMin('kamar', 'harga')->withCount(['kamar as kamar_tersedia_count' => fn ($q) => $q->where('status', 'tersedia')]);
        $query->whereHas('wilayah', fn ($q) => $q->whereIn('nama', ['Jatiwaringin', 'Kalibata', 'Margonda']));
        $query->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%'.$request->string('q').'%'));
        $query->when($request->filled('wilayah_id'), fn ($q) => $q->where('wilayah_id', $request->integer('wilayah_id')));
        if ($request->filled('min_harga') || $request->filled('max_harga') || $request->boolean('tersedia')) {
            $query->whereHas('kamar', function ($k) use ($request) {
                $k->when($request->filled('min_harga'), fn ($k) => $k->where('harga', '>=', $request->input('min_harga')))
                    ->when($request->filled('max_harga'), fn ($k) => $k->where('harga', '<=', $request->input('max_harga')))
                    ->when($request->boolean('tersedia'), fn ($k) => $k->where('status', 'tersedia'));
            });
        }
        $query->when($request->filled('fasilitas_id'), fn ($q) => $q->whereHas('fasilitas', fn ($f) => $f->whereKey($request->integer('fasilitas_id'))));
        $query->when($request->boolean('tersedia'), fn ($q) => $q->whereHas('kamar', fn ($k) => $k->where('status', 'tersedia')));
        $query->when($request->input('sort') === 'termurah', fn ($q) => $q->orderBy('kamar_min_harga'));
        $query->when($request->input('sort') === 'termahal', fn ($q) => $q->orderByDesc('kamar_min_harga'));
        $kos = $query->latest()->paginate(12)->withQueryString();

        return view('public.kos.index_v2', ['kos' => $kos, 'wilayah' => Wilayah::whereIn('nama', ['Jatiwaringin', 'Kalibata', 'Margonda'])->orderBy('nama')->get(), 'fasilitas' => Fasilitas::orderBy('nama')->get()]);
    }

    public function wilayah(Wilayah $wilayah, Request $request): View
    {
        $request->merge(['wilayah_id' => $wilayah->id]);

        return $this->index($request);
    }

    public function show(Kos $kos): View
    {
        app(OnlineRentalService::class)->expire();
        abort_unless($kos->status, 404);
        abort_unless(in_array($kos->wilayah->nama, ['Jatiwaringin', 'Kalibata', 'Margonda'], true), 404);
        $kos->load(['wilayah', 'fasilitas', 'fotoKos' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order'), 'kamar' => fn ($q) => $q->where('status', 'tersedia')->orderBy('harga')]);

        return view('public.kos.show_v2', compact('kos'));
    }
}
