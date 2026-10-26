<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';

    protected $fillable = [
        'kos_id', 'kode', 'harga', 'harga_berlaku_mulai', 'harga_berlaku_sampai', 'status',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'harga_berlaku_mulai' => 'date',
        'harga_berlaku_sampai' => 'date',
    ];

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class);
    }

    public function penyewaan(): HasMany
    {
        return $this->hasMany(Penyewaan::class);
    }
}