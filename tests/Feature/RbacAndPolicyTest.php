<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RbacAndPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth:sanctum', 'role:pemilik'])->get('/test-pemilik-only', function () {
            return response()->json(['message' => 'OK']);
        });

        Route::middleware(['auth:sanctum', 'role:admin'])->get('/test-admin-only', function () {
            return response()->json(['message' => 'OK']);
        });
    }

    public function test_unauthenticated_user_cannot_access_role_protected_route(): void
    {
        $response = $this->getJson('/test-pemilik-only');

        $response->assertStatus(401);
    }

    public function test_inactive_user_is_forbidden(): void
    {
        $inactivePemilik = Pengguna::factory()->pemilik()->nonaktif()->create();
        Sanctum::actingAs($inactivePemilik);

        $response = $this->getJson('/test-pemilik-only');

        $response->assertStatus(403)
            ->assertJson(['message' => 'Akun Anda dinonaktifkan.']);
    }

    public function test_user_with_wrong_role_is_forbidden(): void
    {
        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        $response = $this->getJson('/test-pemilik-only');

        $response->assertStatus(403)
            ->assertJson(['message' => 'Akses ditolak untuk peran Anda.']);
    }

    public function test_user_with_correct_role_can_access(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        Sanctum::actingAs($pemilik);

        $response = $this->getJson('/test-pemilik-only');

        $response->assertStatus(200)
            ->assertJson(['message' => 'OK']);
    }

    public function test_kos_policy_permissions(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $otherUser = Pengguna::factory()->pemilik()->create();
        $admin = Pengguna::factory()->admin()->create();

        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);

        $this->assertTrue(Gate::forUser($owner)->allows('update', $kos));
        $this->assertTrue(Gate::forUser($owner)->allows('delete', $kos));

        $this->assertFalse(Gate::forUser($otherUser)->allows('update', $kos));
        $this->assertFalse(Gate::forUser($otherUser)->allows('delete', $kos));

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $kos));
    }

    public function test_kamar_policy_permissions(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $otherUser = Pengguna::factory()->pemilik()->create();

        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        $this->assertTrue(Gate::forUser($owner)->allows('update', $kamar));
        $this->assertTrue(Gate::forUser($owner)->allows('delete', $kamar));

        $this->assertFalse(Gate::forUser($otherUser)->allows('update', $kamar));
        $this->assertFalse(Gate::forUser($otherUser)->allows('delete', $kamar));
    }

    public function test_pemesanan_policy_permissions(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $pencari = Pengguna::factory()->create();
        $otherPencari = Pengguna::factory()->create();

        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);

        $this->assertTrue(Gate::forUser($pencari)->allows('view', $pemesanan));
        $this->assertTrue(Gate::forUser($owner)->allows('view', $pemesanan));
        $this->assertFalse(Gate::forUser($otherPencari)->allows('view', $pemesanan));

        // Pemilik approve/reject
        $this->assertTrue(Gate::forUser($owner)->allows('updateStatus', [$pemesanan, 'disetujui']));
        $this->assertTrue(Gate::forUser($owner)->allows('updateStatus', [$pemesanan, 'ditolak']));
        $this->assertFalse(Gate::forUser($owner)->allows('updateStatus', [$pemesanan, 'dibatalkan']));

        // Pencari cancel if 'menunggu'
        $this->assertTrue(Gate::forUser($pencari)->allows('updateStatus', [$pemesanan, 'dibatalkan']));
        $this->assertFalse(Gate::forUser($pencari)->allows('updateStatus', [$pemesanan, 'disetujui']));

        // Pencari cannot cancel if already approved
        $pemesanan->update(['status' => 'disetujui']);
        $this->assertFalse(Gate::forUser($pencari)->allows('updateStatus', [$pemesanan, 'dibatalkan']));
    }
}
