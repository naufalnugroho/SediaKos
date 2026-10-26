<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'penyewaan_id', 'nomor_referensi', 'periode_mulai', 'periode_akhir', 'nominal',
        'tanggal_terbit', 'tanggal_jatuh_tempo', 'status',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'periode_mulai' => 'date',
        'periode_akhir' => 'date',
        'tanggal_terbit' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    public function penyewaan(): BelongsTo
    {
        return $this->belongsTo(Penyewaan::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function pembayaranValid(): HasMany
    {
        return $this->pembayaran()->where('status', 'valid');
    }

    public function totalPembayaranValid(): string
    {
        return (string) $this->pembayaranValid()->sum('nominal');
    }

    public function sisaNominal(): string
    {
        return (string) max(0, (float) $this->nominal - (float) $this->totalPembayaranValid());
    }
}