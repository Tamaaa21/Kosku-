<?php

namespace Tests\Feature;

use App\Models\HeroBanner;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HeroBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_fetch_active_hero_banners_only(): void
    {
        HeroBanner::factory()->create([
            'judul' => 'Banner 1 Aktif',
            'urutan' => 1,
            'is_active' => true,
        ]);

        HeroBanner::factory()->create([
            'judul' => 'Banner 2 Nonaktif',
            'urutan' => 2,
            'is_active' => false,
        ]);

        HeroBanner::factory()->create([
            'judul' => 'Banner 3 Aktif',
            'urutan' => 3,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/hero-banners');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.judul', 'Banner 1 Aktif')
            ->assertJsonPath('data.1.judul', 'Banner 3 Aktif');
    }

    public function test_guest_and_non_admin_cannot_manage_hero_banners(): void
    {
        $banner = HeroBanner::factory()->create();
        $pencari = Pengguna::factory()->create(['role' => 'pencari']);

        // Guest unauthenticated
        $this->getJson('/api/v1/admin/hero-banners')->assertStatus(401);
        $this->postJson('/api/v1/admin/hero-banners', [])->assertStatus(401);

        // Pencari authenticated (forbidden)
        Sanctum::actingAs($pencari);
        $this->getJson('/api/v1/admin/hero-banners')->assertStatus(403);
        $this->postJson('/api/v1/admin/hero-banners', [
            'gambar_url' => 'https://example.com/banner.jpg',
        ])->assertStatus(403);
        $this->deleteJson('/api/v1/admin/hero-banners/'.$banner->id)->assertStatus(403);
    }

    public function test_admin_can_crud_hero_banners(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        Sanctum::actingAs($admin);

        // 1. Create banner
        $createResponse = $this->postJson('/api/v1/admin/hero-banners', [
            'judul' => 'Kos Nyaman, Hidup Lebih Mudah',
            'subjudul' => 'Temukan kos terbaik sesuai kebutuhanmu.',
            'badge_text' => 'Cari kos di seluruh Indonesia',
            'gambar_url' => 'https://example.com/hd-bedroom.jpg',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.judul', 'Kos Nyaman, Hidup Lebih Mudah')
            ->assertJsonPath('data.is_active', true);

        $bannerId = $createResponse->json('data.id');

        // 2. Read all banners
        $this->getJson('/api/v1/admin/hero-banners')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // 3. Update banner
        $updateResponse = $this->putJson('/api/v1/admin/hero-banners/'.$bannerId, [
            'judul' => 'Judul Diperbarui',
            'subjudul' => 'Subjudul diperbarui',
            'gambar_url' => 'https://example.com/updated.jpg',
            'urutan' => 2,
            'is_active' => true,
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.judul', 'Judul Diperbarui')
            ->assertJsonPath('data.urutan', 2);

        // 4. Toggle status
        $statusResponse = $this->patchJson('/api/v1/admin/hero-banners/'.$bannerId.'/status', [
            'is_active' => false,
        ]);

        $statusResponse->assertStatus(200)
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('hero_banners', [
            'id' => $bannerId,
            'is_active' => false,
        ]);

        // 5. Delete banner
        $deleteResponse = $this->deleteJson('/api/v1/admin/hero-banners/'.$bannerId);
        $deleteResponse->assertStatus(200);

        $this->assertDatabaseMissing('hero_banners', [
            'id' => $bannerId,
        ]);
    }
}
