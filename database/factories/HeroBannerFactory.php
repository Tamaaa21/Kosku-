<?php

namespace Database\Factories;

use App\Models\HeroBanner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeroBanner>
 */
class HeroBannerFactory extends Factory
{
    protected $model = HeroBanner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul' => 'Kos Nyaman, Hidup Lebih Mudah',
            'subjudul' => 'Temukan kos terbaik sesuai kebutuhanmu. Lokasi strategis, fasilitas lengkap, harga transparan.',
            'badge_text' => 'Cari kos di seluruh Indonesia',
            'gambar_url' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=2070&auto=format&fit=crop',
            'urutan' => 1,
            'is_active' => true,
        ];
    }
}
