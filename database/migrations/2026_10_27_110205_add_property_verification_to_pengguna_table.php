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
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('nik', 30)->nullable()->after('telepon');
            $table->string('foto_ktp', 255)->nullable()->after('foto');
            $table->string('bukti_properti', 255)->nullable()->after('foto_ktp');
            $table->string('foto_properti', 255)->nullable()->after('bukti_properti');
            $table->text('catatan_verifikasi')->nullable()->after('foto_properti');
            $table->string('status_verifikasi', 30)->default('unverified')->after('catatan_verifikasi');
            $table->text('alasan_penolakan')->nullable()->after('status_verifikasi');
            $table->timestamp('verified_at')->nullable()->after('alasan_penolakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropColumn([
                'nik',
                'foto_ktp',
                'bukti_properti',
                'foto_properti',
                'catatan_verifikasi',
                'status_verifikasi',
                'alasan_penolakan',
                'verified_at',
            ]);
        });
    }
};
