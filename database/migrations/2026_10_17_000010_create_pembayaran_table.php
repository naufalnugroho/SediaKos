<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->restrictOnDelete();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_bayar');
            $table->string('metode', 50);
            $table->string('referensi')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 30)->default('valid')->index();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();

            $table->index(['tagihan_id', 'status']);
            $table->index('tanggal_bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};