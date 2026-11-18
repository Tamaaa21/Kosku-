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
        Schema::create('mutasi_saldo_mitra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('pemesanan_id')->nullable()->constrained('pemesanan')->nullOnDelete();
            $table->foreignId('penarikan_dana_id')->nullable()->constrained('penarikan_dana')->nullOnDelete();
            $table->string('tipe', 20)->index(); // 'kredit', 'debit'
            $table->unsignedBigInteger('nominal');
            $table->unsignedBigInteger('saldo_akhir');
            $table->string('deskripsi', 255);
            $table->timestamps();

            $table->index(['pengguna_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_saldo_mitra');
    }
};
