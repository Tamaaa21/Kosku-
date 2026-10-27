<?php

namespace Database\Factories;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'pencari',
            'status_verifikasi' => 'verified',
            'verified_at' => now(),
            'aktif' => true,
        ];
    }

    /**
     * Indicate that the model is a pemilik.
     */
    public function pemilik(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pemilik',
            'status_verifikasi' => 'verified',
            'verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the pemilik is not verified yet.
     */
    public function unverifiedOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pemilik',
            'status_verifikasi' => 'unverified',
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the pemilik is pending verification review.
     */
    public function pendingVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pemilik',
            'status_verifikasi' => 'pending',
            'nik' => '3328012345678901',
            'foto_ktp' => 'https://backend-kos.ddev.site/storage/verifikasi/ktp.jpg',
            'bukti_properti' => 'https://backend-kos.ddev.site/storage/verifikasi/pbb.jpg',
            'foto_properti' => 'https://backend-kos.ddev.site/storage/verifikasi/depan.jpg',
            'catatan_verifikasi' => 'PBB atas nama keluarga',
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the model is nonaktif.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'aktif' => false,
        ]);
    }
}
