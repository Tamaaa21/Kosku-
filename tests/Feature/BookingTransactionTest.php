<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_pencari_can_book_available_room(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        $response = $this->postJson('/api/v1/pemesanan', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->addDays(5)->toDateString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'status', 'kamar', 'kos', 'pengguna'],
            ])
            ->assertJsonPath('data.status', 'menunggu')
            ->assertJsonPath('data.kamar.id', $kamar->id);

        $this->assertDatabaseHas('pemesanan', [
            'pengguna_id' => $pencari->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_atomic_conflict_prevents_double_booking_on_same_room(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id]);

        $pencariA = Pengguna::factory()->create();
        $pencariB = Pengguna::factory()->create();

        // Pencari A berhasil memesan terlebih dahulu
        Pemesanan::factory()->create([
            'pengguna_id' => $pencariA->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);

        // Pencari B mencoba memesan kamar yang sama
        Sanctum::actingAs($pencariB);
        $response = $this->postJson('/api/v1/pemesanan', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'Kamar tidak tersedia atau sedang dalam proses pemesanan.',
            ]);
    }

    public function test_cannot_book_occupied_room(): void
    {
        $kamar = Kamar::factory()->terisi()->create();
        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        $response = $this->postJson('/api/v1/pemesanan', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->addDays(2)->toDateString(),
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'Kamar tidak tersedia atau sedang dalam proses pemesanan.',
            ]);
    }

    public function test_pemilik_can_approve_booking_and_room_becomes_terisi(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);

        Sanctum::actingAs($pemilik);
        $response = $this->patchJson('/api/v1/pemesanan/'.$pemesanan->id.'/status', [
            'status' => 'disetujui',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'disetujui');

        // Pastikan status kamar berubah menjadi 'terisi' secara otomatis
        $this->assertEquals('terisi', $kamar->fresh()->status);
    }

    public function test_pemilik_can_reject_booking(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);

        Sanctum::actingAs($pemilik);
        $response = $this->patchJson('/api/v1/pemesanan/'.$pemesanan->id.'/status', [
            'status' => 'ditolak',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'ditolak');

        $this->assertEquals('tersedia', $kamar->fresh()->status);
    }

    public function test_pencari_can_cancel_pending_booking(): void
    {
        $pencari = Pengguna::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'status' => 'menunggu',
        ]);

        Sanctum::actingAs($pencari);
        $response = $this->patchJson('/api/v1/pemesanan/'.$pemesanan->id.'/status', [
            'status' => 'dibatalkan',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'dibatalkan');
    }

    public function test_pencari_cannot_cancel_already_approved_booking(): void
    {
        $pencari = Pengguna::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'status' => 'disetujui',
        ]);

        Sanctum::actingAs($pencari);
        $response = $this->patchJson('/api/v1/pemesanan/'.$pemesanan->id.'/status', [
            'status' => 'dibatalkan',
        ]);

        $response->assertStatus(403);
    }

    public function test_pencari_and_pemilik_booking_index_isolation(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        $pencariA = Pengguna::factory()->create();
        $pencariB = Pengguna::factory()->create();

        Pemesanan::factory()->create([
            'pengguna_id' => $pencariA->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
        ]);

        Pemesanan::factory()->create([
            'pengguna_id' => $pencariB->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
        ]);

        // Pencari A hanya melihat pemesanan miliknya (1)
        Sanctum::actingAs($pencariA);
        $this->getJson('/api/v1/pemesanan')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Pemilik melihat semua pemesanan kos miliknya (2)
        Sanctum::actingAs($pemilik);
        $this->getJson('/api/v1/pemesanan')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_pemilik_cannot_approve_booking_if_room_is_already_occupied(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->terisi()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);

        Sanctum::actingAs($pemilik);
        $response = $this->patchJson('/api/v1/pemesanan/'.$pemesanan->id.'/status', [
            'status' => 'disetujui',
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'Tidak dapat menyetujui pemesanan karena kamar sudah terisi oleh penyewa lain.',
            ]);
    }
}
