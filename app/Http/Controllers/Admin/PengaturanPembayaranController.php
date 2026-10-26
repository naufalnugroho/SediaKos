<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanPembayaran;
use Illuminate\Http\Request;

class PengaturanPembayaranController extends Controller
{
    public function edit()
    {
        return view('admin.pembayaran.pengaturan', ['rekening' => PengaturanPembayaran::rekening()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'bank' => ['required', 'string', 'max:100'],
            'rekening' => ['required', 'string', 'regex:/^[0-9 -]+$/', 'max:50'],
            'pemilik' => ['required', 'string', 'max:150'],
        ]);
        PengaturanPembayaran::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Rekening pembayaran berhasil disimpan dan ditampilkan kepada penyewa.');
    }
}
