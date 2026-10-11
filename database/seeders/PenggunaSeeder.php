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
                'role' => 'admin',
                'aktif' => true,
            ]
        );

        Pengguna::firstOrCreate(
            ['email' => 'pemilik@kos.test'],
            [
                'nama' => 'Pemilik Kos',
                'password' => Hash::make('password'),
                'role' => 'pemilik',
                'aktif' => true,
            ]
        );

        Pengguna::firstOrCreate(
            ['email' => 'pencari@kos.test'],
            [
                'nama' => 'Pencari Kos',
                'password' => Hash::make('password'),
                'role' => 'pencari',
                'aktif' => true,
            ]
        );
    }
}
