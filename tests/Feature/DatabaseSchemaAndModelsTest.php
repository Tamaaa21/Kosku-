<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaAndModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_expected_database_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('pengguna'));
        $this->assertTrue(Schema::hasTable('kos'));
        $this->assertTrue(Schema::hasTable('kamar'));
        $this->assertTrue(Schema::hasTable('fasilitas'));
        $this->assertTrue(Schema::hasTable('kos_fasilitas'));
        $this->assertTrue(Schema::hasTable('pemesanan'));
    }

    public function test_pengguna_model_and_relations(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        $pencari = Pengguna::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
        ]);

        $this->assertTrue($pemilik->kos->contains($kos));
        $this->assertTrue($pencari->pemesanan->contains($pemesanan));
        $this->assertEquals('pemilik', $pemilik->role);
        $this->assertTrue($pemilik->aktif);
    }

    public function test_kos_model_and_relations(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);
        $fasilitas = Fasilitas::factory()->create();
        $kos->fasilitas()->attach($fasilitas->id);

        $pencari = Pengguna::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
        ]);

        $this->assertEquals($pemilik->id, $kos->pemilik->id);
        $this->assertTrue($kos->kamar->contains($kamar));
        $this->assertTrue($kos->fasilitas->contains($fasilitas));
        $this->assertTrue($kos->pemesanan->contains($pemesanan));
    }

    public function test_kamar_model_and_relations(): void
    {
        $kos = Kos::factory()->create();
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);
        $pencari = Pengguna::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
        ]);

        $this->assertEquals($kos->id, $kamar->kos->id);
        $this->assertTrue($kamar->pemesanan->contains($pemesanan));
    }

    public function test_fasilitas_model_and_relations(): void
    {
        $kos = Kos::factory()->create();
        $fasilitas = Fasilitas::factory()->create();
        $fasilitas->kos()->attach($kos->id);

        $this->assertTrue($fasilitas->kos->contains($kos));
    }

    public function test_pemesanan_model_and_relations(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);
        $pencari = Pengguna::factory()->create();

        $pemesanan = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);

        $this->assertEquals($pencari->id, $pemesanan->pengguna->id);
        $this->assertEquals($kos->id, $pemesanan->kos->id);
        $this->assertEquals($kamar->id, $pemesanan->kamar->id);
        $this->assertEquals('menunggu', $pemesanan->status);
    }

    public function test_deleting_kos_cascades_to_kamar_and_pivot(): void
    {
        $kos = Kos::factory()->create();
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);
        $fasilitas = Fasilitas::factory()->create();
        $kos->fasilitas()->attach($fasilitas->id);

        $kosId = $kos->id;
        $kamarId = $kamar->id;

        $kos->delete();

        $this->assertDatabaseMissing('kamar', ['id' => $kamarId]);
        $this->assertDatabaseMissing('kos_fasilitas', ['kos_id' => $kosId]);
    }
}
