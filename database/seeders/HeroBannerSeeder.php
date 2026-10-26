<?php

namespace Database\Seeders;

use App\Models\HeroBanner;
use Illuminate\Database\Seeder;

class HeroBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'judul' => 'Kos Nyaman, Hidup Lebih Mudah',
                'subjudul' => 'Temukan kos terbaik sesuai kebutuhanmu. Lokasi strategis, fasilitas lengkap, harga transparan.',
                'badge_text' => 'Cari kos di seluruh Indonesia',
                'gambar_url' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=2070&auto=format&fit=crop',
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'judul' => 'Hunian Idaman, Dekat Kampus & Kantor',
                'subjudul' => 'Fasilitas lengkap dengan akses mudah, WiFi super cepat, dan lingkungan nyaman.',
                'badge_text' => 'Pilihan Hunian Eksklusif & Nyaman',
                'gambar_url' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=2071&auto=format&fit=crop',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'judul' => 'Pesan Kamar Kos, Langsung Huni',
                'subjudul' => 'Kemudahan reservasi langsung ke pemilik properti tanpa biaya tersembunyi.',
                'badge_text' => 'Sewa Transparan Bebas Cemas',
                'gambar_url' => 'https://images.unsplash.com/photo-1540518614846-7ede433c4ef7?q=80&w=2071&auto=format&fit=crop',
                'urutan' => 3,
                'is_active' => true,
            ],
            [
                'judul' => 'Kenyamanan Maksimal, Harga Bersahabat',
                'subjudul' => 'Temukan kamar sewa dengan ulasan riil dan foto kondisi asli yang terverifikasi.',
                'badge_text' => 'Ribuan Pilihan Kos Terverifikasi',
                'gambar_url' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=2070&auto=format&fit=crop',
                'urutan' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            HeroBanner::updateOrCreate(
                ['gambar_url' => $banner['gambar_url']],
                $banner
            );
        }
    }
}
