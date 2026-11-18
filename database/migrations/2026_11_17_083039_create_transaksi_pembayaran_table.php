<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanan')->cascadeOnDelete();
            $table->string('kode_transaksi', 64)->unique()->index();
            $table->string('tipe', 20)->index(); // 'dp', 'pelunasan'
            $table->unsignedBigInteger('nominal_pokok');
            $table->unsignedBigInteger('biaya_admin')->default(0);
            $table->unsignedBigInteger('total_tagihan');
            $table->string('snap_token', 255)->nullable();
            $table->string('snap_redirect_url', 500)->nullable();
            $table->string('metode_pembayaran', 50)->nullable();
            $table->string('bank', 30)->nullable();
            $table->string('va_number', 64)->nullable();
            $table->string('status', 30)->default('pending')->index(); // 'pending', 'settlement', 'expire', 'cancel', 'failure'
            $table->json('payload_callback')->nullable();
            $table->timestamp('dibayar_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pembayaran');
    }
};
