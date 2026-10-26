<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kos extends Model
{
    use HasFactory;

    protected $table = 'kos';

    protected $fillable = [
        'wilayah_id', 'nama', 'slug', 'alamat', 'deskripsi', 'aturan', 'kontak', 'status',
    ];

    protected $casts = ['status' => 'boolean'];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function fotoKos(): HasMany
    {
        return $this->hasMany(FotoKos::class);
    }

    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'kos_fasilitas');
    }

    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class);
    }
}