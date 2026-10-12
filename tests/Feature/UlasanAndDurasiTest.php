<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UlasanAndDurasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pencari_can_review_kos_after_booking_approved(): void
    {
        $pemilik = Pengguna::factory()->create(['role' => 'pemilik', 'aktif' => true]);
        $pencari = Pengguna::factory()->create(['role' => 'pencari', 'aktif' => true]);

        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id, 'harga' => 1000000]);

        // Buat pemesanan yang disetujui
        Pemesanan::create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->subDays(5)->format('Y-m-d'),
            'status' => 'disetujui',
        ]);

        Sanctum::actingAs($pencari);

        $response = $this->postJson("/api/v1/kos/{$kos->id}/ulasan", [
            'rating' => 5,
            'komentar' => 'Kos sangat bersih, fasilitas lengkap dan pemilik sangat ramah!',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('rating_rata_rata', 5)
            ->assertJsonPath('ulasan_count', 1);

        $this->assertDatabaseHas('ulasan', [
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'rating' => 5,
        ]);
    }

    public function test_pencari_cannot_review_kos_without_approved_booking(): void
    {
        $pemilik = Pengguna::factory()->create(['role' => 'pemilik', 'aktif' => true]);
        $pencari = Pengguna::factory()->create(['role' => 'pencari', 'aktif' => true]);

        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        // Pemesanan masih 'menunggu' (belum disetujui)
        Pemesanan::create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->format('Y-m-d'),
            'status' => 'menunggu',
        ]);

        Sanctum::actingAs($pencari);

        $response = $this->postJson("/api/v1/kos/{$kos->id}/ulasan", [
            'rating' => 4,
            'komentar' => 'Mencoba memberi ulasan sebelum disetujui.',
        ]);

        $response->assertStatus(403);
    }

    public function test_review_eligibility_endpoint_returns_correct_status(): void
    {
        $pemilik = Pengguna::factory()->create(['role' => 'pemilik', 'aktif' => true]);
        $pencari = Pengguna::factory()->create(['role' => 'pencari', 'aktif' => true]);

        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        Sanctum::actingAs($pencari);

        // Awalnya tidak eligible
        $res1 = $this->getJson("/api/v1/kos/{$kos->id}/ulasan/eligibility");
        $res1->assertStatus(200)
            ->assertJsonPath('eligible', false)
            ->assertJsonPath('has_reviewed', false);

        // Setelah ada pemesanan disetujui
        Pemesanan::create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->format('Y-m-d'),
            'status' => 'disetujui',
        ]);

        $res2 = $this->getJson("/api/v1/kos/{$kos->id}/ulasan/eligibility");
        $res2->assertStatus(200)
            ->assertJsonPath('eligible', true)
            ->assertJsonPath('has_reviewed', false);
    }

    public function test_booking_with_daily_duration_calculates_correct_end_date_and_total_price(): void
    {
        $pemilik = Pengguna::factory()->create(['role' => 'pemilik', 'aktif' => true]);
        $pencari = Pengguna::factory()->create(['role' => 'pencari', 'aktif' => true]);

        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create([
            'kos_id' => $kos->id,
            'status' => 'tersedia',
            'harga' => 1500000,
            'harga_harian' => 100000,
            'harga_mingguan' => 500000,
            'durasi_sewa' => ['harian', 'mingguan', 'bulanan'],
        ]);

        Sanctum::actingAs($pencari);

        $startDate = now()->addDays(2)->format('Y-m-d');

        $response = $this->postJson('/api/v1/pemesanan', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => $startDate,
            'durasi_tipe' => 'harian',
            'durasi_jumlah' => 3,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.durasi_tipe', 'harian')
            ->assertJsonPath('data.durasi_jumlah', 3)
            ->assertJsonPath('data.total_harga', 300000); // 100000 * 3

        $this->assertDatabaseHas('pemesanan', [
            'kamar_id' => $kamar->id,
            'durasi_tipe' => 'harian',
            'durasi_jumlah' => 3,
            'total_harga' => 300000,
        ]);
    }

    public function test_kos_and_kamar_can_store_and_output_duration_prices(): void
    {
        $pemilik = Pengguna::factory()->create(['role' => 'pemilik', 'aktif' => true]);
        Sanctum::actingAs($pemilik);

        $resKos = $this->postJson('/api/v1/kos', [
            'nama' => 'Kos Multi Durasi',
            'alamat' => 'Jl. Kaliurang KM 5',
            'jenis' => 'campur',
            'harga' => 1200000,
            'harga_harian' => 85000,
            'harga_mingguan' => 450000,
            'durasi_sewa' => ['harian', 'mingguan', 'bulanan'],
        ]);

        $resKos->assertStatus(201)
            ->assertJsonPath('data.harga_harian', 85000)
            ->assertJsonPath('data.harga_mingguan', 450000)
            ->assertJsonPath('data.durasi_sewa', ['harian', 'mingguan', 'bulanan']);

        $kosId = $resKos->json('data.id');

        $resKamar = $this->postJson("/api/v1/kos/{$kosId}/kamar", [
            'nama_kamar' => 'Kamar Deluxe 01',
            'harga' => 1500000,
            'harga_harian' => 100000,
            'harga_mingguan' => 550000,
            'durasi_sewa' => ['harian', 'bulanan'],
        ]);

        $resKamar->assertStatus(201)
            ->assertJsonPath('data.harga_harian', 100000)
            ->assertJsonPath('data.harga_mingguan', 550000)
            ->assertJsonPath('data.durasi_sewa', ['harian', 'bulanan']);

        // Add a second room with lower price to verify automatic sync to parent Kos
        $resKamar2 = $this->postJson("/api/v1/kos/{$kosId}/kamar", [
            'nama_kamar' => 'Kamar Standard 02',
            'harga' => 900000,
            'harga_harian' => 60000,
            'harga_mingguan' => 350000,
            'durasi_sewa' => ['mingguan', 'bulanan'],
        ]);
        $resKamar2->assertStatus(201);

        $kosUpdated = Kos::find($kosId);
        $this->assertEquals(900000, $kosUpdated->harga); // Automatically synced to lowest room price
        $this->assertEquals(60000, $kosUpdated->harga_harian);
        $this->assertEquals(350000, $kosUpdated->harga_mingguan);
        $this->assertContains('harian', $kosUpdated->durasi_sewa);
        $this->assertContains('mingguan', $kosUpdated->durasi_sewa);
        $this->assertContains('bulanan', $kosUpdated->durasi_sewa);
    }
}
