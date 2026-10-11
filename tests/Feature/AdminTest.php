<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_amenities(): void
    {
        Fasilitas::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/fasilitas');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_create_update_and_delete_amenity(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $pencari = Pengguna::factory()->create();

        // Non-admin fails
        Sanctum::actingAs($pencari);
        $this->postJson('/api/v1/fasilitas', ['nama' => 'Kolam Renang'])
            ->assertStatus(403);

        // Admin succeeds creating
        Sanctum::actingAs($admin);
        $createResponse = $this->postJson('/api/v1/fasilitas', [
            'nama' => 'Kolam Renang',
            'deskripsi' => 'Kolam renang air hangat',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.nama', 'Kolam Renang');

        $fasilitasId = $createResponse->json('data.id');

        // Admin succeeds updating
        $this->putJson('/api/v1/fasilitas/'.$fasilitasId, [
            'nama' => 'Kolam Renang Mewah',
        ])->assertStatus(200)
            ->assertJsonPath('data.nama', 'Kolam Renang Mewah');

        // Admin succeeds deleting
        $this->deleteJson('/api/v1/fasilitas/'.$fasilitasId)
            ->assertStatus(200);

        $this->assertDatabaseMissing('fasilitas', ['id' => $fasilitasId]);
    }

    public function test_admin_can_list_and_filter_users(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        Pengguna::factory()->pemilik()->count(2)->create();
        Pengguna::factory()->count(3)->create(); // pencari

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/pengguna?role=pemilik');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $targetUser = Pengguna::factory()->create(['aktif' => true]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson('/api/v1/admin/pengguna/'.$targetUser->id.'/status', [
            'aktif' => false,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.aktif', false);

        $this->assertFalse($targetUser->fresh()->aktif);
    }
}
