<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewaan_id')->constrained('penyewaan')->restrictOnDelete();
            $table->string('nomor_referensi')->unique();
            $table->date('periode_mulai');
            $table->date('periode_akhir');
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_terbit');
            $table->date('tanggal_jatuh_tempo');
            $table->string('status', 30)->default('belum_dibayar')->index();
            $table->timestamps();

            $table->index(['penyewaan_id', 'periode_mulai', 'periode_akhir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};