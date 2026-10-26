<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penyewaan;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function index(): View { return view('admin.tagihan.index', ['tagihan' => Tagihan::with('penyewaan.penghuni')->latest()->paginate(15)]); }
    public function create(): View { return view('admin.tagihan.form', ['tagihan' => new Tagihan(), 'penyewaan' => Penyewaan::with(['penghuni', 'kamar.kos'])->where('status', 'aktif')->get()]); }
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['penyewaan_id' => ['required', 'exists:penyewaan,id'], 'nomor_referensi' => ['required', 'string', 'max:100', 'unique:tagihan,nomor_referensi'], 'periode_mulai' => ['required', 'date'], 'periode_akhir' => ['required', 'date', 'after_or_equal:periode_mulai'], 'nominal' => ['required', 'numeric', 'min:0.01'], 'tanggal_terbit' => ['required', 'date'], 'tanggal_jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal_terbit']]);
        Tagihan::create(array_merge($data, ['status' => 'belum_dibayar']));
        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dibuat.');
    }
}