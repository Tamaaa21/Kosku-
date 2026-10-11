<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Kos;
use App\Models\Pemesanan;
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

    public function test_admin_can_retrieve_single_user_detail_with_relations(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $pemilik = Pengguna::factory()->pemilik()->create();
        Kos::factory()->count(2)->create(['pemilik_id' => $pemilik->id]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/pengguna/'.$pemilik->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $pemilik->id)
            ->assertJsonPath('data.kos_count', 2)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nama',
                    'email',
                    'telepon',
                    'role',
                    'aktif',
                    'kos_count',
                    'pemesanan_count',
                    'daftar_kos',
                    'riwayat_pemesanan',
                ],
            ]);
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

    public function test_admin_can_retrieve_system_stats(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        Pemesanan::factory()->count(3)->create(['status' => 'disetujui']);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_pengguna',
                    'total_pencari',
                    'total_pemilik',
                    'pengguna_aktif',
                    'pengguna_nonaktif',
                    'total_kos',
                    'total_kamar',
                    'kamar_tersedia',
                    'kamar_terisi',
                    'okupansi_rate',
                    'total_pemesanan',
                    'pemesanan_menunggu',
                    'pemesanan_disetujui',
                    'pemesanan_ditolak',
                    'total_nilai_transaksi',
                    'distribusi_jenis',
                    'tren_harian',
                    'pemesanan_terbaru',
                ],
            ]);
    }
}
