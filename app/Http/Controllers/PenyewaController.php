<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\PengaturanPembayaran;
use App\Models\Penyewaan;
use App\Models\Tagihan;
use App\Services\OnlineRentalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PenyewaController extends Controller
{
    public function index(Request $request, OnlineRentalService $service)
    {
        $service->expire();

        $request->validate(['status' => ['nullable', 'in:berjalan,riwayat']]);
        $base = Penyewaan::where('user_id', $request->user()->id);
        $query = (clone $base)->with(['kamar.kos.wilayah', 'tagihan.pembayaran' => fn ($q) => $q->latest()]);
        if ($request->input('status') === 'berjalan') {
            $query->whereIn('status', ['menunggu_pembayaran', 'menunggu_verifikasi', 'aktif']);
        }
        if ($request->input('status') === 'riwayat') {
            $query->whereIn('status', ['selesai', 'batal', 'kedaluwarsa']);
        }

        return view('penyewa.index', [
            'penyewaan' => $query->latest()->paginate(10)->withQueryString(),
            'rekening' => PengaturanPembayaran::rekening(),
            'jumlahAktif' => (clone $base)->where('status', 'aktif')->count(),
            'jumlahMenunggu' => (clone $base)->whereIn('status', ['menunggu_pembayaran', 'menunggu_verifikasi'])->count(),
            'jumlahSelesai' => (clone $base)->whereIn('status', ['selesai', 'batal', 'kedaluwarsa'])->count(),
        ]);
    }

    public function create(Request $request, Kamar $kamar, OnlineRentalService $service)
    {
        if ($request->user()->role !== 'penyewa') {
            return view('penyewa.ganti-akun', compact('kamar'));
        }
        $service->expire();
        abort_unless($kamar->fresh()->status === 'tersedia' && $kamar->kos->status, 404);
        abort_unless(in_array($kamar->kos->wilayah->nama, ['Jatiwaringin', 'Kalibata', 'Margonda'], true), 404);

        return view('penyewa.create', compact('kamar'));
    }

    public function store(Request $request, Kamar $kamar, OnlineRentalService $service)
    {
        if ($request->user()->role !== 'penyewa') {
            return redirect()->route('penyewa.create', $kamar)
                ->with('error', 'Pemesanan kamar memerlukan akun penyewa. Silakan ganti akun terlebih dahulu.');
        }
        $data = $request->validate([
            'telepon' => ['required', 'string', 'max:30'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'durasi' => ['required', 'integer', 'between:1,12'],
        ]);
        $service->book($request->user(), $kamar, $data);

        return redirect()->route('penyewa.index')->with('success', 'Pemesanan diterima. Upload bukti pembayaran dalam 24 jam.');
    }

    public function upload(Request $request, Tagihan $tagihan, OnlineRentalService $service)
    {
        abort_unless($tagihan->penyewaan->user_id === $request->user()->id, 403);
        $service->expire();
        $request->validate([
            'bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'tanggal_bayar' => ['required', 'date', 'after_or_equal:'.$tagihan->tanggal_terbit->toDateString(), 'before_or_equal:today'],
        ]);
        $path = $request->file('bukti')->store('bukti-pembayaran', 'local');
        if (! $path) {
            throw ValidationException::withMessages(['bukti' => 'Bukti gagal disimpan. Silakan coba lagi.']);
        }
        try {
            DB::transaction(function () use ($request, $tagihan, $path) {
                $rental = $tagihan->penyewaan;
                Kamar::lockForUpdate()->findOrFail($rental->kamar_id);
                $rental->refresh();
                $tagihan = Tagihan::lockForUpdate()->findOrFail($tagihan->id);
                if ($rental->status !== 'menunggu_pembayaran' || $rental->batas_bayar->isPast() ||
                    $tagihan->pembayaran()->where('status', 'menunggu_verifikasi')->exists() || $tagihan->sisaNominal() <= 0) {
                    throw ValidationException::withMessages(['bukti' => 'Pemesanan tidak dapat menerima bukti pembayaran saat ini.']);
                }
                $tagihan->pembayaran()->create([
                    'nominal' => $tagihan->sisaNominal(), 'tanggal_bayar' => $request->input('tanggal_bayar'),
                    'metode' => 'Transfer', 'status' => 'menunggu_verifikasi', 'bukti_path' => $path,
                ]);
                $rental->update(['status' => 'menunggu_verifikasi']);
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return back()->with('success', 'Bukti diterima. Pembayaran menunggu verifikasi owner.');
    }

    public function bukti(Request $request, Pembayaran $pembayaran)
    {
        abort_unless($request->user()->isAdmin() || $pembayaran->tagihan->penyewaan->user_id === $request->user()->id, 403);
        abort_unless($pembayaran->bukti_path && Storage::disk('local')->exists($pembayaran->bukti_path), 404);

        return Storage::disk('local')->download($pembayaran->bukti_path, 'bukti-pembayaran-'.$pembayaran->id.'.'.pathinfo($pembayaran->bukti_path, PATHINFO_EXTENSION), ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function preview(Request $request, Pembayaran $pembayaran)
    {
        abort_unless($request->user()->isAdmin() || $pembayaran->tagihan->penyewaan->user_id === $request->user()->id, 403);
        abort_unless($pembayaran->bukti_path && Storage::disk('local')->exists($pembayaran->bukti_path), 404, 'File bukti tidak ditemukan.');
        $mime = Storage::disk('local')->mimeType($pembayaran->bukti_path);
        abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'application/pdf'], true), 415);

        return response()->file(Storage::disk('local')->path($pembayaran->bukti_path), [
            'Content-Type' => $mime, 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; frame-ancestors 'self'", 'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    public function cancel(Request $request, Penyewaan $penyewaan)
    {
        abort_unless($penyewaan->user_id === $request->user()->id, 403);
        DB::transaction(function () use ($penyewaan) {
            $room = Kamar::lockForUpdate()->findOrFail($penyewaan->kamar_id);
            $penyewaan->refresh();
            abort_unless($penyewaan->status === 'menunggu_pembayaran', 409);
            $penyewaan->update(['status' => 'batal']);
            $penyewaan->tagihan()->update(['status' => 'dibatalkan']);
            if (! $room->penyewaan()->whereIn('status', ['aktif', 'menunggu_pembayaran', 'menunggu_verifikasi'])->exists()) {
                $room->update(['status' => 'tersedia']);
            }
        });

        return back()->with('success', 'Pemesanan dibatalkan.');
    }
}
