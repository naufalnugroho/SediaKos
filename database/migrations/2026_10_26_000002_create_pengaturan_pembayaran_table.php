<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_pembayaran', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('bank', 100);
            $table->string('rekening', 50);
            $table->string('pemilik', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pembayaran');
    }
};
