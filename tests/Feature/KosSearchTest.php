<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KosSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_paginate_kos(): void
    {
        Kos::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/kos?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nama', 'alamat', 'harga', 'jenis', 'pemilik', 'fasilitas'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_can_search_kos_by_keyword(): void
    {
        Kos::factory()->create(['nama' => 'Kos Melati Indah', 'alamat' => 'Sleman']);
        Kos::factory()->create(['nama' => 'Kos Mawar Asri', 'alamat' => 'Bantul']);

        $response = $this->getJson('/api/v1/kos?q=Melati');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama', 'Kos Melati Indah');
    }

    public function test_can_filter_kos_by_price_and_type(): void
    {
        Kos::factory()->create(['nama' => 'Kos Murah Putra', 'harga' => 500000, 'jenis' => 'putra']);
        Kos::factory()->create(['nama' => 'Kos Mahal Putra', 'harga' => 2000000, 'jenis' => 'putra']);
        Kos::factory()->create(['nama' => 'Kos Murah Putri', 'harga' => 500000, 'jenis' => 'putri']);

        $response = $this->getJson('/api/v1/kos?harga_min=400000&harga_max=1000000&jenis=putra');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama', 'Kos Murah Putra');
    }

    public function test_can_filter_kos_by_facility(): void
    {
        $wifi = Fasilitas::factory()->create(['nama' => 'Wi-Fi']);
        $ac = Fasilitas::factory()->create(['nama' => 'AC']);

        $kosWithWifi = Kos::factory()->create(['nama' => 'Kos Ada Wifi']);
        $kosWithWifi->fasilitas()->attach($wifi->id);

        $kosWithAc = Kos::factory()->create(['nama' => 'Kos Ada AC']);
        $kosWithAc->fasilitas()->attach($ac->id);

        $response = $this->getJson('/api/v1/kos?fasilitas[]='.$wifi->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama', 'Kos Ada Wifi');
    }

    public function test_can_filter_kos_by_room_availability(): void
    {
        $kosAvailable = Kos::factory()->create(['nama' => 'Kos Tersedia']);
        Kamar::factory()->create(['kos_id' => $kosAvailable->id, 'status' => 'tersedia']);

        $kosFull = Kos::factory()->create(['nama' => 'Kos Penuh']);
        Kamar::factory()->create(['kos_id' => $kosFull->id, 'status' => 'terisi']);

        $response = $this->getJson('/api/v1/kos?tersedia=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama', 'Kos Tersedia');
    }

    public function test_can_view_kos_details(): void
    {
        $kos = Kos::factory()->create();
        $fasilitas = Fasilitas::factory()->create();
        $kos->fasilitas()->attach($fasilitas->id);
        Kamar::factory()->count(2)->create(['kos_id' => $kos->id]);

        $response = $this->getJson('/api/v1/kos/'.$kos->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'nama', 'alamat', 'pemilik', 'fasilitas', 'kamar'],
            ]);
    }

    public function test_pemilik_can_create_kos_with_automatic_geocoding(): void
    {
        Http::fake([
            'https://nominatim.openstreetmap.org/search*' => Http::response([
                [
                    'lat' => '-7.780000',
                    'lon' => '110.370000',
                ],
            ], 200),
        ]);

        $pemilik = Pengguna::factory()->pemilik()->create();
        Sanctum::actingAs($pemilik);

        $response = $this->postJson('/api/v1/kos', [
            'nama' => 'Kos Baru Mandiri',
            'alamat' => 'Jl. Kaliurang KM 6, Sleman',
            'deskripsi' => 'Kos strategis',
            'jenis' => 'campur',
            'harga' => 1200000,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.nama', 'Kos Baru Mandiri')
            ->assertJsonPath('data.latitude', -7.78)
            ->assertJsonPath('data.longitude', 110.37);

        $this->assertDatabaseHas('kos', [
            'nama' => 'Kos Baru Mandiri',
            'pemilik_id' => $pemilik->id,
        ]);
    }

    public function test_pencari_cannot_create_kos(): void
    {
        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        $response = $this->postJson('/api/v1/kos', [
            'nama' => 'Kos Ilegal',
            'alamat' => 'Jl. Bebas',
            'jenis' => 'putra',
            'harga' => 500000,
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_update_own_kos_and_non_owner_is_forbidden(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $otherPemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);

        // Non-owner fails
        Sanctum::actingAs($otherPemilik);
        $this->putJson('/api/v1/kos/'.$kos->id, ['nama' => 'Nama Diretas'])
            ->assertStatus(403);

        // Owner succeeds
        Sanctum::actingAs($owner);
        $this->putJson('/api/v1/kos/'.$kos->id, ['nama' => 'Nama Diperbarui'])
            ->assertStatus(200)
            ->assertJsonPath('data.nama', 'Nama Diperbarui');
    }

    public function test_owner_can_delete_own_kos(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);

        Sanctum::actingAs($owner);
        $response = $this->deleteJson('/api/v1/kos/'.$kos->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('kos', ['id' => $kos->id]);
    }
}
