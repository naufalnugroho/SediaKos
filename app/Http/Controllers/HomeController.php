<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Services\OnlineRentalService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        app(OnlineRentalService::class)->expire();

        return view('welcome', [
            'cabang' => Kos::where('status', true)->whereHas('wilayah', fn ($q) => $q->whereIn('nama', ['Jatiwaringin', 'Kalibata', 'Margonda']))
                ->with(['wilayah', 'fotoKos' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')])->withMin('kamar', 'harga')
                ->withCount(['kamar as tersedia' => fn ($q) => $q->where('status', 'tersedia')])->orderBy('nama')->get(),
        ]);
    }
}
