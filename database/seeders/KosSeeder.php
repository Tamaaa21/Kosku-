<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\Kos;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class KosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pemilik = Pengguna::where('role', 'pemilik')->first();

        if (! $pemilik) {
            $pemilik = Pengguna::factory()->pemilik()->create();
        }

        $fasilitasIds = Fasilitas::pluck('id');

        $kos = Kos::firstOrCreate(
            ['nama' => 'Kos Melati Harmoni', 'pemilik_id' => $pemilik->id],
            [
                'alamat' => 'Jl. Kaliurang KM 5, Sleman, D.I. Yogyakarta',
                'deskripsi' => 'Kos nyaman dekat kampus, lingkungan asri dan tenang.',
                'jenis' => 'putri',
                'harga' => 850000,
                'latitude' => -7.7612345,
                'longitude' => 110.3789012,
            ]
        );

        if ($fasilitasIds->isNotEmpty()) {
            $kos->fasilitas()->sync($fasilitasIds->take(4));
        }
    }
}
