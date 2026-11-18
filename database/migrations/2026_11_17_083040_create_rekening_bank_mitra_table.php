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
        Schema::create('rekening_bank_mitra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('nama_bank', 50);
            $table->string('nomor_rekening', 50);
            $table->string('nama_pemilik', 100);
            $table->boolean('is_utama')->default(false);
            $table->timestamps();

            $table->index(['pengguna_id', 'is_utama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening_bank_mitra');
    }
};
