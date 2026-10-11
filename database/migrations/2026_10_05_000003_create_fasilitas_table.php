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
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('kos_fasilitas', function (Blueprint $table) {
            $table->foreignId('kos_id')->constrained('kos')->cascadeOnDelete();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->cascadeOnDelete();
            $table->primary(['kos_id', 'fasilitas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kos_fasilitas');
        Schema::dropIfExists('fasilitas');
    }
};
