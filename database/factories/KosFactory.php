<?php

namespace Database\Factories;

use App\Models\Kos;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kos>
 */
class KosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pemilik_id' => Pengguna::factory()->pemilik(),
            'nama' => 'Kos '.fake()->company(),
            'alamat' => fake()->address(),
            'deskripsi' => fake()->paragraph(),
            'jenis' => fake()->randomElement(['putra', 'putri', 'campur']),
            'harga' => fake()->numberBetween(500000, 2500000),
            'latitude' => fake()->latitude(-7.8, -7.7),
            'longitude' => fake()->longitude(110.3, 110.4),
        ];
    }

    /**
     * Indicate that the kos is for putra.
     */
    public function putra(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'putra',
        ]);
    }

    /**
     * Indicate that the kos is for putri.
     */
    public function putri(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'putri',
        ]);
    }

    /**
     * Indicate that the kos is campur.
     */
    public function campur(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'campur',
        ]);
    }
}
