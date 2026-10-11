<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Database\Seeder;

class KamarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kosList = Kos::all();

        foreach ($kosList as $kos) {
            Kamar::firstOrCreate(
                ['kos_id' => $kos->id, 'nama_kamar' => 'Kamar A-01'],
                ['harga' => $kos->harga, 'status' => 'tersedia']
            );

            Kamar::firstOrCreate(
                ['kos_id' => $kos->id, 'nama_kamar' => 'Kamar A-02'],
                ['harga' => $kos->harga, 'status' => 'terisi']
            );
        }
    }
}
