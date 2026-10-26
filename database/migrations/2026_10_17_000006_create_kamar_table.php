<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kos_id')->constrained('kos')->restrictOnDelete();
            $table->string('kode');
            $table->decimal('harga', 12, 2);
            $table->date('harga_berlaku_mulai')->nullable();
            $table->date('harga_berlaku_sampai')->nullable();
            $table->string('status', 30)->default('tersedia')->index();
            $table->timestamps();

            $table->unique(['kos_id', 'kode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};