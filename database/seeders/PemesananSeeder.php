<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class PemesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pencari = Pengguna::where('role', 'pencari')->first();
        $kamar = Kamar::where('status', 'tersedia')->first();

        if ($pencari && $kamar) {
            Pemesanan::firstOrCreate(
                [
                    'pengguna_id' => $pencari->id,
                    'kos_id' => $kamar->kos_id,
                    'kamar_id' => $kamar->id,
                ],
                [
                    'tanggal_pemesanan' => now()->addDays(7)->toDateString(),
                    'status' => 'menunggu',
                ]
            );
        }
    }
}
