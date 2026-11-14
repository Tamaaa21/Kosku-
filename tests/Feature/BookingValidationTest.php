<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_rejects_past_date_with_422(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        $response = $this->postJson('/api/v1/bookings', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->subDay()->toDateString(),
            'durasi_tipe' => 'bulanan',
            'durasi_jumlah' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tanggal_pemesanan']);
    }

    public function test_booking_rejects_duration_exceeding_max_limit_with_422(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        // Bulanan maksimal 36 bulan
        $response = $this->postJson('/api/v1/bookings', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->addDay()->toDateString(),
            'durasi_tipe' => 'bulanan',
            'durasi_jumlah' => 40,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['durasi_jumlah']);
    }

    public function test_booking_rejects_duration_less_than_one_with_422(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        $response = $this->postJson('/api/v1/bookings', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->addDay()->toDateString(),
            'durasi_tipe' => 'bulanan',
            'durasi_jumlah' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['durasi_jumlah']);
    }

    public function test_booking_accepts_durasi_sewa_alias_for_backward_compatibility(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id, 'harga' => 1200000]);

        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        $response = $this->postJson('/api/v1/bookings', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->addDay()->toDateString(),
            'durasi_sewa' => 3,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.durasi_jumlah', 3)
            ->assertJsonPath('data.total_harga', 3600000);
    }

    public function test_export_csv_is_forbidden_for_pencari_role(): void
    {
        $pencari = Pengguna::factory()->create();
        Sanctum::actingAs($pencari);

        $response = $this->getJson('/api/v1/bookings/export');
        $response->assertStatus(403);
    }

    public function test_export_csv_is_accessible_by_pemilik_and_streams_csv(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id, 'nama' => 'Kos Pemilik Mawar']);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create(['nama' => 'Budi Santoso']);
        Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
            'total_harga' => 1500000,
        ]);

        Sanctum::actingAs($pemilik);

        $response = $this->get('/api/v1/bookings/export');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_kos_radius_filter_calculates_distance_accurately(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();

        // Kos Dekat: Monas (-6.1754, 106.8272)
        $kosDekat = Kos::factory()->create([
            'pemilik_id' => $pemilik->id,
            'nama' => 'Kos Ring 1 Monas',
            'latitude' => -6.1754,
            'longitude' => 106.8272,
        ]);

        // Kos Jauh: Bandung (-6.9175, 107.6191)
        $kosJauh = Kos::factory()->create([
            'pemilik_id' => $pemilik->id,
            'nama' => 'Kos Bandung',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
        ]);

        // Cari dari koordinat Gambir (-6.1767, 106.8306), radius 5 km
        $response = $this->getJson('/api/v1/kos?latitude=-6.1767&longitude=106.8306&radius=5');

        $response->assertStatus(200);
        $data = $response->json('data');

        $names = array_column($data, 'nama');
        $this->assertContains('Kos Ring 1 Monas', $names);
        $this->assertNotContains('Kos Bandung', $names);

        // Pastikan atribut jarak_km ada dan akurat
        $monasKos = collect($data)->firstWhere('nama', 'Kos Ring 1 Monas');
        $this->assertNotNull($monasKos['jarak_km']);
        $this->assertLessThan(2.0, $monasKos['jarak_km']);
    }
}
