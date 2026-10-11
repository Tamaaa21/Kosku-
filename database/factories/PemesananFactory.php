<?php

namespace Database\Factories;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pemesanan>
 */
class PemesananFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pengguna_id' => Pengguna::factory(),
            'kos_id' => Kos::factory(),
            'kamar_id' => fn (array $attributes) => Kamar::factory()->create(['kos_id' => $attributes['kos_id']]),
            'tanggal_pemesanan' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'status' => 'menunggu',
        ];
    }

    /**
     * Indicate that the booking is approved.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disetujui',
        ]);
    }

    /**
     * Indicate that the booking is rejected.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function dibatalkan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'dibatalkan',
        ]);
    }
}
