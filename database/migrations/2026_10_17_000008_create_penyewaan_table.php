<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyewaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kamar_id')->constrained('kamar')->restrictOnDelete();
            $table->foreignId('penghuni_id')->constrained('penghuni')->restrictOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->string('status', 30)->default('aktif')->index();
            $table->timestamps();

            $table->index(['kamar_id', 'status']);
            $table->index(['penghuni_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyewaan');
    }
};