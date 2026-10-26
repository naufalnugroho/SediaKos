<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = ['nama'];

    public function kos(): BelongsToMany
    {
        return $this->belongsToMany(Kos::class, 'kos_fasilitas');
    }
}