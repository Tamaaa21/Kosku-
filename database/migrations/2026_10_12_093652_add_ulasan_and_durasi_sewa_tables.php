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
            $table->unsignedInteger('harga_harian')->nullable()->after('harga');
            $table->unsignedInteger('harga_mingguan')->nullable()->after('harga_harian');
            $table->json('durasi_sewa')->nullable()->after('harga_mingguan');
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->unsignedInteger('harga_harian')->nullable()->after('harga');
            $table->unsignedInteger('harga_mingguan')->nullable()->after('harga_harian');
            $table->json('durasi_sewa')->nullable()->after('harga_mingguan');
        });

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->string('durasi_tipe', 20)->default('bulanan')->after('tanggal_pemesanan');
            $table->unsignedInteger('durasi_jumlah')->default(1)->after('durasi_tipe');
            $table->date('tanggal_selesai')->nullable()->after('durasi_jumlah');
            $table->unsignedBigInteger('total_harga')->nullable()->after('tanggal_selesai');
        });

        Schema::create('ulasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('kos_id')->constrained('kos')->cascadeOnDelete();
            $table->foreignId('pemesanan_id')->nullable()->constrained('pemesanan')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('komentar');
            $table->timestamps();

            $table->unique(['pengguna_id', 'kos_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasan');

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropColumn(['durasi_tipe', 'durasi_jumlah', 'tanggal_selesai', 'total_harga']);
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->dropColumn(['harga_harian', 'harga_mingguan', 'durasi_sewa']);
        });

        Schema::table('kos', function (Blueprint $table) {
            $table->dropColumn(['harga_harian', 'harga_mingguan', 'durasi_sewa']);
        });
    }
};
