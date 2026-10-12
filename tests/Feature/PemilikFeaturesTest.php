<?php

namespace Tests\Feature;

use App\Models\Kos;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PemilikFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pemilik_can_upload_images_up_to_5_files_and_max_2mb(): void
    {
        Storage::fake('public');

        $pemilik = Pengguna::factory()->create([
            'role' => 'pemilik',
            'aktif' => true,
        ]);
        Sanctum::actingAs($pemilik);

        $response = $this->postJson('/api/v1/upload/images', [
            'images' => [
                UploadedFile::fake()->image('kos1.jpg', 800, 600)->size(500),
                UploadedFile::fake()->image('kos2.png', 800, 600)->size(1500),
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'urls',
            ]);

        $urls = $response->json('urls');
        $this->assertCount(2, $urls);
    }

    public function test_upload_images_fails_if_file_exceeds_2mb(): void
    {
        Storage::fake('public');

        $pemilik = Pengguna::factory()->create([
            'role' => 'pemilik',
            'aktif' => true,
        ]);
        Sanctum::actingAs($pemilik);

        // 3000 KB > 2048 KB limit
        $response = $this->postJson('/api/v1/upload/images', [
            'images' => [
                UploadedFile::fake()->image('huge.jpg', 1200, 1200)->size(3000),
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images.0']);
    }

    public function test_upload_images_fails_if_more_than_5_files(): void
    {
        Storage::fake('public');

        $pemilik = Pengguna::factory()->create([
            'role' => 'pemilik',
            'aktif' => true,
        ]);
        Sanctum::actingAs($pemilik);

        $images = [];
        for ($i = 0; $i < 6; $i++) {
            $images[] = UploadedFile::fake()->image("img_{$i}.jpg", 400, 400)->size(100);
        }

        $response = $this->postJson('/api/v1/upload/images', [
            'images' => $images,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images']);
    }

    public function test_pencari_cannot_upload_images(): void
    {
        Storage::fake('public');

        $pencari = Pengguna::factory()->create([
            'role' => 'pencari',
            'aktif' => true,
        ]);
        Sanctum::actingAs($pencari);

        $response = $this->postJson('/api/v1/upload/images', [
            'images' => [
                UploadedFile::fake()->image('test.jpg', 400, 400)->size(100),
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_pemilik_can_create_and_update_kos_with_rules_and_photos(): void
    {
        $pemilik = Pengguna::factory()->create([
            'role' => 'pemilik',
            'aktif' => true,
        ]);
        Sanctum::actingAs($pemilik);

        $rules = ['Dilarang merokok di dalam kamar', 'Gerbang tutup jam 23.00', 'Tamu lawan jenis dilarang menginap'];
        $photos = ['/storage/uploads/foto1.jpg', '/storage/uploads/foto2.jpg'];

        $storeResponse = $this->postJson('/api/v1/kos', [
            'nama' => 'Kos Graha Asri Syariah',
            'alamat' => 'Jl. Kaliurang KM 5 No. 12, Sleman, Yogyakarta',
            'deskripsi' => 'Kos nyaman dengan lingkungan tenang.',
            'jenis' => 'putri',
            'harga' => 1200000,
            'latitude' => -7.7654321,
            'longitude' => 110.3789012,
            'peraturan' => $rules,
            'foto' => $photos,
        ]);

        $storeResponse->assertStatus(201)
            ->assertJsonPath('data.nama', 'Kos Graha Asri Syariah')
            ->assertJsonPath('data.peraturan', $rules)
            ->assertJsonPath('data.foto', $photos);

        $kosId = $storeResponse->json('data.id');

        // Update with updated rules and photos
        $newRules = ['Dilarang merokok', 'Wajib menjaga kebersihan'];
        $newPhotos = ['/storage/uploads/foto_baru.jpg'];

        $updateResponse = $this->putJson("/api/v1/kos/{$kosId}", [
            'peraturan' => $newRules,
            'foto' => $newPhotos,
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.peraturan', $newRules)
            ->assertJsonPath('data.foto', $newPhotos);
    }

    public function test_pemilik_can_create_and_update_kamar_with_size_photos_and_amenities(): void
    {
        $pemilik = Pengguna::factory()->create([
            'role' => 'pemilik',
            'aktif' => true,
        ]);
        Sanctum::actingAs($pemilik);

        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);

        $storeResponse = $this->postJson("/api/v1/kos/{$kos->id}/kamar", [
            'nama_kamar' => 'Kamar Deluxe A1',
            'harga' => 1500000,
            'status' => 'tersedia',
            'ukuran' => '3 x 4 m',
            'foto' => ['/storage/uploads/kamar1.jpg', '/storage/uploads/kamar2.jpg'],
            'fasilitas_kamar' => ['Kasur Queen', 'AC', 'Kamar Mandi Dalam', 'Meja Kerja'],
        ]);

        $storeResponse->assertStatus(201)
            ->assertJsonPath('data.nama_kamar', 'Kamar Deluxe A1')
            ->assertJsonPath('data.ukuran', '3 x 4 m')
            ->assertJsonPath('data.foto', ['/storage/uploads/kamar1.jpg', '/storage/uploads/kamar2.jpg'])
            ->assertJsonPath('data.fasilitas_kamar', ['Kasur Queen', 'AC', 'Kamar Mandi Dalam', 'Meja Kerja']);

        $kamarId = $storeResponse->json('data.id');

        $updateResponse = $this->putJson("/api/v1/kamar/{$kamarId}", [
            'ukuran' => '4 x 4 m',
            'status' => 'terisi',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.ukuran', '4 x 4 m')
            ->assertJsonPath('data.status', 'terisi');
    }
}
