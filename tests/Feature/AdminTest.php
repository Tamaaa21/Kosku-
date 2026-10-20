<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Kamar;
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

        $response = $this->getJson('/api/v1/facilities');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_create_update_and_delete_amenity(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $pencari = Pengguna::factory()->create();

        // Non-admin fails
        Sanctum::actingAs($pencari);
        $this->postJson('/api/v1/facilities', ['nama' => 'Kolam Renang'])
            ->assertStatus(403);

        // Admin succeeds creating
        Sanctum::actingAs($admin);
        $createResponse = $this->postJson('/api/v1/facilities', [
            'nama' => 'Kolam Renang',
            'deskripsi' => 'Kolam renang air hangat',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.nama', 'Kolam Renang');

        $fasilitasId = $createResponse->json('data.id');

        // Admin succeeds updating
        $this->putJson('/api/v1/facilities/'.$fasilitasId, [
            'nama' => 'Kolam Renang Mewah',
        ])->assertStatus(200)
            ->assertJsonPath('data.nama', 'Kolam Renang Mewah');

        // Admin succeeds deleting
        $this->deleteJson('/api/v1/facilities/'.$fasilitasId)
            ->assertStatus(200);

        $this->assertDatabaseMissing('fasilitas', ['id' => $fasilitasId]);
    }

    public function test_pemilik_can_create_amenity_tag_but_cannot_update_or_delete(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $fasilitas = Fasilitas::factory()->create();

        Sanctum::actingAs($pemilik);

        // Pemilik can create facility
        $createResponse = $this->postJson('/api/v1/facilities', [
            'nama' => 'Gym & Fitness Center',
            'deskripsi' => 'Fasilitas kebugaran kos',
        ]);
        $createResponse->assertStatus(201)
            ->assertJsonPath('data.nama', 'Gym & Fitness Center');

        // Pemilik cannot update or delete master catalog
        $this->putJson('/api/v1/facilities/'.$fasilitas->id, ['nama' => 'Beda Nama'])
            ->assertStatus(403);
        $this->deleteJson('/api/v1/facilities/'.$fasilitas->id)
            ->assertStatus(403);
    }

    public function test_admin_can_list_and_filter_users(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        Pengguna::factory()->pemilik()->count(2)->create();
        Pengguna::factory()->count(3)->create(); // pencari

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/users?role=pemilik');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_admin_can_retrieve_single_user_detail_with_relations(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $pemilik = Pengguna::factory()->pemilik()->create();
        Kos::factory()->count(2)->create(['pemilik_id' => $pemilik->id]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/users/'.$pemilik->id);

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

        $response = $this->patchJson('/api/v1/admin/users/'.$targetUser->id.'/status', [
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

    public function test_admin_can_list_and_filter_all_kos(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $pencari = Pengguna::factory()->create();
        $pemilik = Pengguna::factory()->pemilik()->create(['nama' => 'Pak Bambang']);

        $kosPutri = Kos::factory()->create([
            'pemilik_id' => $pemilik->id,
            'nama' => 'Kos Melati Cantik',
            'jenis' => 'putri',
            'harga' => 800000,
        ]);
        Kamar::factory()->create(['kos_id' => $kosPutri->id, 'status' => 'tersedia']);
        Kamar::factory()->create(['kos_id' => $kosPutri->id, 'status' => 'terisi']);

        $kosPutra = Kos::factory()->create([
            'nama' => 'Kos Garuda Sakti',
            'jenis' => 'putra',
            'harga' => 500000,
        ]);

        // Pencari cannot access admin kos index
        Sanctum::actingAs($pencari);
        $this->getJson('/api/v1/admin/kos')->assertStatus(403);

        // Admin can list all
        Sanctum::actingAs($admin);
        $response = $this->getJson('/api/v1/admin/kos');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Admin can filter by jenis
        $responseJenis = $this->getJson('/api/v1/admin/kos?jenis=putri');
        $responseJenis->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $kosPutri->id)
            ->assertJsonPath('data.0.kamar_count', 2)
            ->assertJsonPath('data.0.kamar_tersedia_count', 1);

        // Admin can search by owner name
        $responseSearch = $this->getJson('/api/v1/admin/kos?q=Bambang');
        $responseSearch->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $kosPutri->id);
    }

    public function test_admin_can_delete_kos_for_moderation(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $pencari = Pengguna::factory()->create();
        $pemilik = Pengguna::factory()->pemilik()->create();

        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);
        $fasilitas = Fasilitas::factory()->create();
        $kos->fasilitas()->attach($fasilitas->id);

        Pemesanan::factory()->create([
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'pengguna_id' => $pencari->id,
        ]);

        // Pencari cannot moderate kos
        Sanctum::actingAs($pencari);
        $this->deleteJson('/api/v1/admin/kos/'.$kos->id)->assertStatus(403);

        // Admin deletes kos successfully with cascading relations
        Sanctum::actingAs($admin);
        $response = $this->deleteJson('/api/v1/admin/kos/'.$kos->id);
        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('kos', ['id' => $kos->id]);
        $this->assertDatabaseMissing('kamar', ['id' => $kamar->id]);
        $this->assertDatabaseMissing('pemesanan', ['kos_id' => $kos->id]);
        $this->assertDatabaseMissing('kos_fasilitas', ['kos_id' => $kos->id]);
    }

    public function test_admin_can_filter_and_search_all_bookings(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        $pencari = Pengguna::factory()->create(['nama' => 'Siti Nurhaliza']);
        $kos = Kos::factory()->create(['nama' => 'Kos Melati Harmoni']);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        $booking = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);

        Sanctum::actingAs($admin);

        // Search by user name
        $res = $this->getJson('/api/v1/bookings?q=Siti');
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $booking->id);

        // Filter by status
        $resStatus = $this->getJson('/api/v1/bookings?status=menunggu');
        $resStatus->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Admin can update status
        $resUpdate = $this->patchJson('/api/v1/bookings/'.$booking->id.'/status', [
            'status' => 'disetujui',
        ]);
        $resUpdate->assertStatus(200)
            ->assertJsonPath('data.status', 'disetujui');

        $this->assertEquals('disetujui', $booking->fresh()->status);
        $this->assertEquals('terisi', $kamar->fresh()->status);
    }
}
