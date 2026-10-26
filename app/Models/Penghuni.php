<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penghuni extends Model
{
    use HasFactory;

    protected $table = 'penghuni';

    protected $fillable = ['nama', 'telepon', 'catatan', 'status'];

    public function penyewaan(): HasMany
    {
        return $this->hasMany(Penyewaan::class);
    }
}