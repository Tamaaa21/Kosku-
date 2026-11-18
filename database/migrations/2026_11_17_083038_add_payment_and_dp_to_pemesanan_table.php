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
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->string('status', 30)->default('menunggu')->change();

            if (! Schema::hasColumn('pemesanan', 'persentase_dp')) {
                $table->unsignedTinyInteger('persentase_dp')->default(20);
            }
            if (! Schema::hasColumn('pemesanan', 'nominal_dp')) {
                $table->unsignedBigInteger('nominal_dp')->default(0);
            }
            if (! Schema::hasColumn('pemesanan', 'sisa_pelunasan')) {
                $table->unsignedBigInteger('sisa_pelunasan')->default(0);
            }
            if (! Schema::hasColumn('pemesanan', 'komisi_persen')) {
                $table->unsignedTinyInteger('komisi_persen')->default(5);
            }
            if (! Schema::hasColumn('pemesanan', 'komisi_nominal')) {
                $table->unsignedBigInteger('komisi_nominal')->default(0);
            }
            if (! Schema::hasColumn('pemesanan', 'total_hak_mitra')) {
                $table->unsignedBigInteger('total_hak_mitra')->default(0);
            }
            if (! Schema::hasColumn('pemesanan', 'status_pembayaran')) {
                $table->string('status_pembayaran', 30)->default('belum_dibayar')->index();
            }
            if (! Schema::hasColumn('pemesanan', 'kunci_diserahkan_at')) {
                $table->timestamp('kunci_diserahkan_at')->nullable();
            }
            if (! Schema::hasColumn('pemesanan', 'catatan_serah_terima')) {
                $table->text('catatan_serah_terima')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('pemesanan', 'pemesanan_status_pembayaran_index')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                $table->dropIndex('pemesanan_status_pembayaran_index');
            });
        }

        Schema::table('pemesanan', function (Blueprint $table) {
            $existing = Schema::getColumnListing('pemesanan');
            $columnsToDrop = array_intersect(
                [
                    'persentase_dp',
                    'nominal_dp',
                    'sisa_pelunasan',
                    'komisi_persen',
                    'komisi_nominal',
                    'total_hak_mitra',
                    'status_pembayaran',
                    'kunci_diserahkan_at',
                    'catatan_serah_terima',
                ],
                $existing
            );

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
