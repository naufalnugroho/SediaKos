<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Kos;
use App\Models\Wilayah;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'jumlahWilayah' => Wilayah::count(),
            'jumlahKosAktif' => Kos::where('status', true)->count(),
            'jumlahFasilitas' => Fasilitas::count(),
            'kosUnggulan' => Kos::where('status', true)
                ->with('wilayah')
                ->withCount(['kamar as kamar_tersedia_count' => fn ($query) => $query->where('status', 'tersedia')])
                ->latest()
                ->take(3)
                ->get(),
        ]);
    }
}