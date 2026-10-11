<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengguna::firstOrCreate(
            ['email' => 'admin@kos.test'],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('password'),
                'telepon' => '081234567890',
                'role' => 'admin',
                'aktif' => true,
            ]
        );

        Pengguna::firstOrCreate(
            ['email' => 'pemilik@kos.test'],
            [
                'nama' => 'Pemilik Kos',
                'password' => Hash::make('password'),
                'telepon' => '081298765432',
                'role' => 'pemilik',
                'aktif' => true,
            ]
        );

        Pengguna::firstOrCreate(
            ['email' => 'pencari@kos.test'],
            [
                'nama' => 'Pencari Kos',
                'password' => Hash::make('password'),
                'telepon' => '081311223344',
                'role' => 'pencari',
                'aktif' => true,
            ]
        );
    }
}
