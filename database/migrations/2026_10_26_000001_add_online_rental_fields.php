<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewaan', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamp('batas_bayar')->nullable()->index();
        });
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('bukti_path')->nullable();
            $table->text('alasan_penolakan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', fn (Blueprint $table) => $table->dropColumn(['bukti_path', 'alasan_penolakan']));
        Schema::table('penyewaan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('batas_bayar');
        });
    }
};
