<?php

namespace Tests\Feature;

use App\Models\Kos;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_owner_cannot_create_kos(): void
    {
        $owner = Pengguna::factory()->unverifiedOwner()->create();

        $response = $this->actingAs($owner)->postJson('/api/v1/kos', [
            'nama' => 'Kos Baru Belum Verifikasi',
            'alamat' => 'Jl. Merdeka No. 1, Tegal',
            'jenis' => 'campur',
            'harga' => 1000000,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status_verifikasi' => 'unverified',
            ]);
    }

    public function test_verified_owner_can_create_kos(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();

        $response = $this->actingAs($owner)->postJson('/api/v1/kos', [
            'nama' => 'Kos Baru Terverifikasi',
            'alamat' => 'Jl. Merdeka No. 1, Tegal',
            'jenis' => 'campur',
            'harga' => 1000000,
        ]);

        $response->assertStatus(201);
    }

    public function test_owner_can_view_verification_status(): void
    {
        $owner = Pengguna::factory()->unverifiedOwner()->create();

        $response = $this->actingAs($owner)->getJson('/api/v1/pemilik/verification');

        $response->assertStatus(200)
            ->assertJsonPath('data.status_verifikasi', 'unverified')
            ->assertJsonPath('data.is_verified', false);
    }

    public function test_owner_can_submit_verification_documents(): void
    {
        $owner = Pengguna::factory()->unverifiedOwner()->create();

        $response = $this->actingAs($owner)->postJson('/api/v1/pemilik/verification', [
            'nik' => '3328012345678901',
            'foto_ktp' => 'https://backend-kos.ddev.site/storage/verifikasi/ktp.jpg',
            'bukti_properti' => 'https://backend-kos.ddev.site/storage/verifikasi/pbb.jpg',
            'foto_properti' => 'https://backend-kos.ddev.site/storage/verifikasi/depan.jpg',
            'catatan_verifikasi' => 'PBB atas nama almarhum ayah',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('pengguna', [
            'id' => $owner->id,
            'status_verifikasi' => 'pending',
            'nik' => '3328012345678901',
        ]);
    }

    public function test_admin_can_list_and_review_verification_requests(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $owner = Pengguna::factory()->pendingVerification()->create();

        // 1. Admin lists verifications
        $listResponse = $this->actingAs($admin)->getJson('/api/v1/admin/verifications');
        $listResponse->assertStatus(200);

        // 2. Admin approves verification
        $approveResponse = $this->actingAs($admin)->patchJson("/api/v1/admin/verifications/{$owner->id}/status", [
            'status' => 'verified',
        ]);

        $approveResponse->assertStatus(200);

        $this->assertDatabaseHas('pengguna', [
            'id' => $owner->id,
            'status_verifikasi' => 'verified',
        ]);

        // 3. Now owner can create kos
        $createKosResponse = $this->actingAs($owner->fresh())->postJson('/api/v1/kos', [
            'nama' => 'Kos Sukses Terverifikasi',
            'alamat' => 'Jl. Ahmad Yani No. 10, Tegal',
            'jenis' => 'putra',
            'harga' => 850000,
        ]);

        $createKosResponse->assertStatus(201);
    }

    public function test_admin_can_reject_verification_with_reason(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $owner = Pengguna::factory()->pendingVerification()->create();

        $response = $this->actingAs($admin)->patchJson("/api/v1/admin/verifications/{$owner->id}/status", [
            'status' => 'rejected',
            'alasan_penolakan' => 'Foto KTP buram dan tidak terbaca jelas.',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('pengguna', [
            'id' => $owner->id,
            'status_verifikasi' => 'rejected',
            'alasan_penolakan' => 'Foto KTP buram dan tidak terbaca jelas.',
        ]);
    }
}
