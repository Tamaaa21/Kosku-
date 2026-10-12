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
        Schema::table('kos', function (Blueprint $table) {
            $table->json('peraturan')->nullable()->after('deskripsi');
            $table->json('foto')->nullable()->after('harga');
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->string('ukuran', 30)->nullable()->after('nama_kamar');
            $table->json('foto')->nullable()->after('status');
            $table->json('fasilitas_kamar')->nullable()->after('foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kos', function (Blueprint $table) {
            $table->dropColumn(['peraturan', 'foto']);
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->dropColumn(['ukuran', 'foto', 'fasilitas_kamar']);
        });
    }
};
