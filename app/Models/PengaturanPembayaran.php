<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanPembayaran extends Model
{
    protected $table = 'pengaturan_pembayaran';

    public $incrementing = false;

    protected $fillable = ['id', 'bank', 'rekening', 'pemilik'];

    public static function rekening(): array
    {
        $setting = static::find(1);

        return $setting ? $setting->only(['bank', 'rekening', 'pemilik']) : config('pembayaran');
    }
}
