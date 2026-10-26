<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoKos extends Model
{
    use HasFactory;

    protected $table = 'foto_kos';

    protected $fillable = [
        'kos_id', 'path', 'original_name', 'mime_type', 'size', 'is_primary', 'sort_order',
    ];

    protected $casts = ['is_primary' => 'boolean'];

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class);
    }
}