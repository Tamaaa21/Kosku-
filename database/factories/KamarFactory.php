<?php

namespace Database\Factories;

use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kamar>
 */
class KamarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kos_id' => Kos::factory(),
            'nama_kamar' => 'Kamar '.fake()->bothify('?-##'),
            'harga' => fake()->numberBetween(500000, 2500000),
            'status' => 'tersedia',
        ];
    }

    /**
     * Indicate that the room is available.
     */
    public function tersedia(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'tersedia',
        ]);
    }

    /**
     * Indicate that the room is occupied.
     */
    public function terisi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terisi',
        ]);
    }
}
