<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemilik_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->text('alamat');
            $table->text('deskripsi')->nullable();
            $table->enum('jenis', ['putra', 'putri', 'campur']);
            $table->unsignedInteger('harga');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['jenis', 'harga']);

            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->fullText(['nama', 'alamat']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kos');
    }
};
