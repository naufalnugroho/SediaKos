<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Penyewaan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OnlineRentalService
{
    public function expire(): void
    {
        Penyewaan::where('status', 'menunggu_pembayaran')->where('batas_bayar', '<', now())
            ->pluck('id')->each(function ($id) {
                DB::transaction(function () use ($id) {
                    $rental = Penyewaan::find($id);
                    $room = Kamar::lockForUpdate()->findOrFail($rental->kamar_id);
                    $rental->refresh();
                    if ($rental->status === 'menunggu_pembayaran' && $rental->batas_bayar->isPast()) {
                        $rental->update(['status' => 'kedaluwarsa']);
                        $rental->tagihan()->update(['status' => 'dibatalkan']);
                        if (! $room->penyewaan()->whereIn('status', ['aktif', 'menunggu_pembayaran', 'menunggu_verifikasi'])->exists()) {
                            $room->update(['status' => 'tersedia']);
                        }
                    }
                });
            });
    }

    public function book(User $user, Kamar $room, array $data): Penyewaan
    {
        $this->expire();

        return DB::transaction(function () use ($user, $room, $data) {
            $room = Kamar::lockForUpdate()->findOrFail($room->id);
            if (! $room->kos->status || ! in_array($room->kos->wilayah->nama, ['Jatiwaringin', 'Kalibata', 'Margonda'], true) || $room->status !== 'tersedia' || $room->harga <= 0 ||
                $room->penyewaan()->whereIn('status', ['aktif', 'menunggu_pembayaran', 'menunggu_verifikasi'])->exists()) {
                throw ValidationException::withMessages(['kamar_id' => 'Kamar sudah tidak tersedia untuk disewa.']);
            }
            $start = Carbon::parse($data['tanggal_mulai']);
            $end = $start->copy()->addMonthsNoOverflow((int) $data['durasi'])->subDay();
            $tenant = Penghuni::create(['nama' => $user->name, 'telepon' => $data['telepon'], 'status' => 'calon']);
            $rental = Penyewaan::create([
                'user_id' => $user->id, 'kamar_id' => $room->id, 'penghuni_id' => $tenant->id,
                'tanggal_mulai' => $start, 'tanggal_akhir' => $end,
                'status' => 'menunggu_pembayaran', 'batas_bayar' => now()->addDay(),
            ]);
            $rental->tagihan()->create([
                'nomor_referensi' => 'INV-'.Str::upper((string) Str::uuid()),
                'periode_mulai' => $start, 'periode_akhir' => $end,
                'nominal' => (int) round((float) $room->harga * 100) * (int) $data['durasi'] / 100,
                'tanggal_terbit' => today(), 'tanggal_jatuh_tempo' => now()->addDay()->toDateString(),
                'status' => 'belum_dibayar',
            ]);
            $room->update(['status' => 'dipesan']);

            return $rental;
        });
    }
}
