<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fasilitas = [
            ['nama' => 'Wi-Fi', 'deskripsi' => 'Koneksi internet nirkabel berkecepatan tinggi.'],
            ['nama' => 'AC', 'deskripsi' => 'Air conditioner / pendingin ruangan.'],
            ['nama' => 'Kamar Mandi Dalam', 'deskripsi' => 'Kamar mandi pribadi di dalam kamar.'],
            ['nama' => 'Kasur', 'deskripsi' => 'Tempat tidur / kasur busa/springbed.'],
            ['nama' => 'Lemari Pakaian', 'deskripsi' => 'Lemari untuk penyimpanan pakaian.'],
            ['nama' => 'Parkir Motor', 'deskripsi' => 'Area parkir aman untuk sepeda motor.'],
            ['nama' => 'Dapur Bersama', 'deskripsi' => 'Fasilitas dapur bersama dilengkapi kompor dan wastafel.'],
        ];

        foreach ($fasilitas as $item) {
            Fasilitas::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
