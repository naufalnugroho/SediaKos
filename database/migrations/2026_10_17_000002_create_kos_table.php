<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained('wilayah')->restrictOnDelete();
            $table->string('nama');
            $table->string('slug')->nullable()->unique();
            $table->text('alamat');
            $table->text('deskripsi')->nullable();
            $table->text('aturan')->nullable();
            $table->string('kontak')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['wilayah_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kos');
    }
};