<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiUriDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_and_profile_uris_resolve_properly(): void
    {
        // 1. POST /api/v1/auth/register & legacy /api/v1/register
        $regResponse = $this->postJson('/api/v1/auth/register', [
            'nama' => 'Test User',
            'email' => 'test.user@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'pencari',
        ]);
        $regResponse->assertStatus(201);

        // 2. POST /api/v1/auth/login & legacy /api/v1/login
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'test.user@example.com',
            'password' => 'secret123',
        ]);
        $loginResponse->assertStatus(200);

        // 3. Authenticated profile endpoints
        $user = Pengguna::where('email', 'test.user@example.com')->first();
        Sanctum::actingAs($user);

        // GET /api/v1/me and GET /api/v1/auth/me
        $this->getJson('/api/v1/me')->assertStatus(200);
        $this->getJson('/api/v1/auth/me')->assertStatus(200);

        // PUT /api/v1/me
        $this->putJson('/api/v1/me', ['nama' => 'Updated User Name'])->assertStatus(200);

        // POST /api/v1/me/avatar
        Storage::fake('public');
        $file = $this->createFakeImage('avatar.jpg');
        $this->postJson('/api/v1/me/avatar', ['foto' => $file])->assertStatus(200);

        // PUT /api/v1/me/password
        $this->putJson('/api/v1/me/password', [
            'current_password' => 'secret123',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ])->assertStatus(200);

        // POST /api/v1/auth/logout
        $this->postJson('/api/v1/auth/logout')->assertStatus(200);
    }

    public function test_kos_and_rooms_uris_resolve_properly(): void
    {
        $pemilik = Pengguna::factory()->create(['role' => 'pemilik', 'aktif' => true]);
        Sanctum::actingAs($pemilik);

        // POST /api/v1/kos
        $kosResponse = $this->postJson('/api/v1/kos', [
            'nama' => 'Kos Baru Nyaman',
            'alamat' => 'Jl. Kaliurang KM 7',
            'jenis' => 'campur',
            'harga' => 1200000,
        ]);
        $kosResponse->assertStatus(201);
        $kosId = $kosResponse->json('data.id');

        // GET /api/v1/kos
        $this->getJson('/api/v1/kos')->assertStatus(200);

        // GET /api/v1/kos/{id}
        $this->getJson("/api/v1/kos/{$kosId}")->assertStatus(200);

        // POST /api/v1/kos/{id}/rooms & legacy /api/v1/kos/{id}/kamar
        $roomResponse = $this->postJson("/api/v1/kos/{$kosId}/rooms", [
            'nama_kamar' => 'Kamar A1',
            'harga' => 1200000,
            'status' => 'tersedia',
        ]);
        $roomResponse->assertStatus(201);
        $roomId = $roomResponse->json('data.id');

        // GET /api/v1/kos/{id}/rooms & legacy /api/v1/kos/{id}/kamar
        $this->getJson("/api/v1/kos/{$kosId}/rooms")->assertStatus(200);
        $this->getJson("/api/v1/kos/{$kosId}/kamar")->assertStatus(200);

        // PUT /api/v1/rooms/{id} & legacy /api/v1/kamar/{id}
        $this->putJson("/api/v1/rooms/{$roomId}", ['nama_kamar' => 'Kamar A1 Super'])->assertStatus(200);
        $this->putJson("/api/v1/kamar/{$roomId}", ['nama_kamar' => 'Kamar A1 Ultra'])->assertStatus(200);

        // PUT /api/v1/kos/{id}
        $this->putJson("/api/v1/kos/{$kosId}", ['nama' => 'Kos Baru Nyaman Updated'])->assertStatus(200);

        // DELETE /api/v1/rooms/{id} & legacy /api/v1/kamar/{id}
        $this->deleteJson("/api/v1/rooms/{$roomId}")->assertStatus(200);

        // DELETE /api/v1/kos/{id}
        $this->deleteJson("/api/v1/kos/{$kosId}")->assertStatus(200);
    }

    public function test_bookings_workflow_uris_resolve_properly(): void
    {
        $pemilik = Pengguna::factory()->create(['role' => 'pemilik', 'aktif' => true]);
        $pencari = Pengguna::factory()->create(['role' => 'pencari', 'aktif' => true]);
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id, 'status' => 'tersedia', 'harga' => 1000000]);

        // POST /api/v1/bookings as pencari
        Sanctum::actingAs($pencari);
        $bookingResponse = $this->postJson('/api/v1/bookings', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->addDay()->format('Y-m-d'),
            'durasi_tipe' => 'bulanan',
            'durasi_jumlah' => 2,
        ]);
        $bookingResponse->assertStatus(201);
        $bookingId = $bookingResponse->json('data.id');

        // GET /api/v1/bookings & legacy /api/v1/pemesanan
        $this->getJson('/api/v1/bookings')->assertStatus(200);
        $this->getJson('/api/v1/pemesanan')->assertStatus(200);

        // GET /api/v1/bookings/{id} & legacy /api/v1/pemesanan/{id}
        $this->getJson("/api/v1/bookings/{$bookingId}")->assertStatus(200);
        $this->getJson("/api/v1/pemesanan/{$bookingId}")->assertStatus(200);

        // PATCH /api/v1/bookings/{id}/status as pemilik (approve)
        Sanctum::actingAs($pemilik);
        $patchResponse = $this->patchJson("/api/v1/bookings/{$bookingId}/status", [
            'status' => 'disetujui',
        ]);
        $patchResponse->assertStatus(200);

        // Check review eligibility & store review as pencari
        Sanctum::actingAs($pencari);
        // GET /api/v1/kos/{id}/reviews/eligibility & legacy /api/v1/kos/{id}/ulasan/eligibility
        $this->getJson("/api/v1/kos/{$kos->id}/reviews/eligibility")
            ->assertStatus(200)
            ->assertJson(['eligible' => true]);
        $this->getJson("/api/v1/kos/{$kos->id}/ulasan/eligibility")
            ->assertStatus(200)
            ->assertJson(['eligible' => true]);

        // POST /api/v1/kos/{id}/reviews & legacy /api/v1/kos/{id}/ulasan
        $this->postJson("/api/v1/kos/{$kos->id}/reviews", [
            'rating' => 5,
            'komentar' => 'Sangat bersih dan nyaman!',
        ])->assertStatus(200);

        // GET /api/v1/kos/{id}/reviews & legacy /api/v1/kos/{id}/ulasan
        $this->getJson("/api/v1/kos/{$kos->id}/reviews")->assertStatus(200);
        $this->getJson("/api/v1/kos/{$kos->id}/ulasan")->assertStatus(200);
    }

    public function test_facilities_and_media_upload_uris_resolve_properly(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'admin', 'aktif' => true]);
        Sanctum::actingAs($admin);

        // GET /api/v1/facilities & legacy /api/v1/fasilitas
        $this->getJson('/api/v1/facilities')->assertStatus(200);
        $this->getJson('/api/v1/fasilitas')->assertStatus(200);

        // POST /api/v1/facilities & legacy /api/v1/fasilitas
        $facResponse = $this->postJson('/api/v1/facilities', [
            'nama' => 'Gym Area',
            'deskripsi' => 'Fasilitas kebugaran',
        ]);
        $facResponse->assertStatus(201);
        $facId = $facResponse->json('data.id');

        // PUT /api/v1/facilities/{id} & legacy /api/v1/fasilitas/{id}
        $this->putJson("/api/v1/facilities/{$facId}", ['nama' => 'Gym & Fitness'])->assertStatus(200);
        $this->putJson("/api/v1/fasilitas/{$facId}", ['nama' => 'Gym Center'])->assertStatus(200);

        // DELETE /api/v1/facilities/{id}
        $this->deleteJson("/api/v1/facilities/{$facId}")->assertStatus(200);

        // POST /api/v1/uploads/images & legacy /api/v1/upload/images
        Storage::fake('public');
        $img = $this->createFakeImage('test.jpg');
        $uploadResp = $this->postJson('/api/v1/uploads/images', ['images' => [$img]]);
        $uploadResp->assertStatus(201)->assertJsonStructure(['urls']);

        $uploadRespLegacy = $this->postJson('/api/v1/upload/images', ['images' => [$img]]);
        $uploadRespLegacy->assertStatus(201)->assertJsonStructure(['urls']);
    }

    public function test_admin_moderation_uris_resolve_properly(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'admin', 'aktif' => true]);
        $targetUser = Pengguna::factory()->create(['role' => 'pencari', 'aktif' => true]);
        $kos = Kos::factory()->create(['pemilik_id' => $admin->id]);

        Sanctum::actingAs($admin);

        // GET /api/v1/admin/users & legacy /api/v1/admin/pengguna
        $this->getJson('/api/v1/admin/users')->assertStatus(200);
        $this->getJson('/api/v1/admin/pengguna')->assertStatus(200);

        // GET /api/v1/admin/users/{id} & legacy /api/v1/admin/pengguna/{id}
        $this->getJson("/api/v1/admin/users/{$targetUser->id}")->assertStatus(200);
        $this->getJson("/api/v1/admin/pengguna/{$targetUser->id}")->assertStatus(200);

        // PATCH /api/v1/admin/users/{id}/status & legacy /api/v1/admin/pengguna/{id}/status
        $this->patchJson("/api/v1/admin/users/{$targetUser->id}/status", ['aktif' => false])->assertStatus(200);
        $this->patchJson("/api/v1/admin/pengguna/{$targetUser->id}/status", ['aktif' => true])->assertStatus(200);

        // GET /api/v1/admin/stats
        $this->getJson('/api/v1/admin/stats')->assertStatus(200);

        // GET /api/v1/admin/kos
        $this->getJson('/api/v1/admin/kos')->assertStatus(200);

        // DELETE /api/v1/admin/kos/{id}
        $this->deleteJson("/api/v1/admin/kos/{$kos->id}")->assertStatus(200);
    }
}
