<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\MutasiSaldoMitra;
use App\Models\Pemesanan;
use App\Models\PenarikanDana;
use App\Models\Pengguna;
use App\Models\RekeningBankMitra;
use App\Models\Ulasan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaginationAndSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_kos_sorting_and_pagination_options(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();

        $kosA = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Alfa Murah',
            'harga' => 500000,
            'created_at' => now()->subDays(5),
        ]);

        $kosB = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Beta Sedang',
            'harga' => 1000000,
            'created_at' => now()->subDays(2),
        ]);

        $kosC = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Zetta Mahal',
            'harga' => 2000000,
            'created_at' => now()->subDay(),
        ]);

        $user = Pengguna::factory()->create();
        Ulasan::create(['kos_id' => $kosB->id, 'pengguna_id' => $user->id, 'rating' => 5, 'komentar' => 'Bagus']);
        Ulasan::create(['kos_id' => $kosA->id, 'pengguna_id' => $user->id, 'rating' => 3, 'komentar' => 'Lumayan']);

        // 1. Pagination Check
        $resPage1 = $this->getJson('/api/v1/kos?per_page=2&page=1');
        $resPage1->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 3);

        $resPage2 = $this->getJson('/api/v1/kos?per_page=2&page=2');
        $resPage2->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 2);

        // 2. Sort by harga_asc
        $resHargaAsc = $this->getJson('/api/v1/kos?sort=harga_asc');
        $resHargaAsc->assertStatus(200);
        $this->assertEquals($kosA->id, $resHargaAsc->json('data.0.id'));
        $this->assertEquals($kosC->id, $resHargaAsc->json('data.2.id'));

        // 3. Sort by harga_desc
        $resHargaDesc = $this->getJson('/api/v1/kos?sort=harga_desc');
        $resHargaDesc->assertStatus(200);
        $this->assertEquals($kosC->id, $resHargaDesc->json('data.0.id'));
        $this->assertEquals($kosA->id, $resHargaDesc->json('data.2.id'));

        // 4. Sort by nama_asc
        $resNamaAsc = $this->getJson('/api/v1/kos?sort=nama_asc');
        $resNamaAsc->assertStatus(200);
        $this->assertEquals($kosA->id, $resNamaAsc->json('data.0.id'));
        $this->assertEquals($kosC->id, $resNamaAsc->json('data.2.id'));

        // 5. Sort by rating_desc
        $resRatingDesc = $this->getJson('/api/v1/kos?sort=rating_desc');
        $resRatingDesc->assertStatus(200);
        $this->assertEquals($kosB->id, $resRatingDesc->json('data.0.id'));
    }

    public function test_room_pagination_filtering_and_sorting(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);

        $room1 = Kamar::factory()->create([
            'kos_id' => $kos->id,
            'nama_kamar' => 'Kamar B10',
            'harga' => 1200000,
            'status' => 'terisi',
        ]);

        $room2 = Kamar::factory()->create([
            'kos_id' => $kos->id,
            'nama_kamar' => 'Kamar A01',
            'harga' => 800000,
            'status' => 'tersedia',
        ]);

        $room3 = Kamar::factory()->create([
            'kos_id' => $kos->id,
            'nama_kamar' => 'Kamar C05',
            'harga' => 1500000,
            'status' => 'tersedia',
        ]);

        // 1. Pagination & Counts verification
        $res = $this->getJson("/api/v1/kos/{$kos->id}/rooms?per_page=2&page=1");
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('counts.total', 3)
            ->assertJsonPath('counts.tersedia', 2)
            ->assertJsonPath('counts.terisi', 1)
            ->assertJsonPath('counts.estimasi_pendapatan', 3500000)
            ->assertJsonPath('counts.pendapatan_aktif', 1200000);

        // 2. Search query filter
        $resSearch = $this->getJson("/api/v1/kos/{$kos->id}/rooms?q=A01");
        $resSearch->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama_kamar', 'Kamar A01');

        // 3. Status filter
        $resStatus = $this->getJson("/api/v1/kos/{$kos->id}/rooms?status=terisi");
        $resStatus->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $room1->id);

        // 4. Sort by nama_asc
        $resSortNama = $this->getJson("/api/v1/kos/{$kos->id}/rooms?sort=nama_asc");
        $resSortNama->assertStatus(200);
        $this->assertEquals($room2->id, $resSortNama->json('data.0.id'));
        $this->assertEquals($room3->id, $resSortNama->json('data.2.id'));

        // 5. Sort by harga_desc
        $resSortHarga = $this->getJson("/api/v1/kos/{$kos->id}/rooms?sort=harga_desc");
        $resSortHarga->assertStatus(200);
        $this->assertEquals($room3->id, $resSortHarga->json('data.0.id'));
        $this->assertEquals($room2->id, $resSortHarga->json('data.2.id'));
    }

    public function test_booking_pagination_and_sorting_for_owner(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $pencari = Pengguna::factory()->create(['nama' => 'Budi Santoso']);
        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        $booking1 = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
            'total_harga' => 500000,
            'tanggal_pemesanan' => now()->addDays(10)->toDateString(),
            'created_at' => now()->subDays(3),
        ]);

        $booking2 = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'aktif',
            'total_harga' => 1500000,
            'tanggal_pemesanan' => now()->addDays(2)->toDateString(),
            'created_at' => now()->subDays(1),
        ]);

        $booking3 = Pemesanan::factory()->create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'selesai',
            'total_harga' => 2000000,
            'tanggal_pemesanan' => now()->subMonth()->toDateString(),
            'created_at' => now(),
        ]);

        Sanctum::actingAs($owner);

        // 1. Pagination & Counts verification
        $res = $this->getJson('/api/v1/bookings?per_page=2&page=1');
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('counts.all', 3)
            ->assertJsonPath('counts.menunggu', 1)
            ->assertJsonPath('counts.aktif', 1)
            ->assertJsonPath('counts.riwayat', 1);

        // 2. Sort by total_desc
        $resTotalDesc = $this->getJson('/api/v1/bookings?sort=total_desc');
        $resTotalDesc->assertStatus(200);
        $this->assertEquals($booking3->id, $resTotalDesc->json('data.0.id'));
        $this->assertEquals($booking1->id, $resTotalDesc->json('data.2.id'));

        // 3. Sort by checkin_asc
        $resCheckin = $this->getJson('/api/v1/bookings?sort=checkin_asc');
        $resCheckin->assertStatus(200);
        $this->assertEquals($booking3->id, $resCheckin->json('data.0.id'));
        $this->assertEquals($booking1->id, $resCheckin->json('data.2.id'));
    }

    public function test_owner_kos_pagination_and_sorting(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();

        $kosA = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Melati',
            'harga' => 500000,
        ]);
        Kamar::factory()->count(2)->create(['kos_id' => $kosA->id, 'status' => 'tersedia']);

        $kosB = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Anggrek',
            'harga' => 800000,
        ]);
        Kamar::factory()->count(5)->create(['kos_id' => $kosB->id, 'status' => 'terisi']);

        $kosC = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Mawar',
            'harga' => 1200000,
        ]);
        Kamar::factory()->create(['kos_id' => $kosC->id, 'status' => 'tersedia']);

        // 1. Pagination & counts
        $res = $this->getJson("/api/v1/kos?pemilik_id={$owner->id}&per_page=2&page=1");
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('counts.total_kos', 3)
            ->assertJsonPath('counts.total_kamar', 8)
            ->assertJsonPath('counts.total_kamar_terisi', 5);

        // 2. Sort kamar_desc
        $resKamar = $this->getJson("/api/v1/kos?pemilik_id={$owner->id}&sort=kamar_desc");
        $resKamar->assertStatus(200);
        $this->assertEquals($kosB->id, $resKamar->json('data.0.id')); // 5 kamar
        $this->assertEquals($kosA->id, $resKamar->json('data.1.id')); // 2 kamar
        $this->assertEquals($kosC->id, $resKamar->json('data.2.id')); // 1 kamar

        // 3. Sort nama_asc
        $resNama = $this->getJson("/api/v1/kos?pemilik_id={$owner->id}&sort=nama_asc");
        $resNama->assertStatus(200);
        $this->assertEquals($kosB->id, $resNama->json('data.0.id')); // Kos Anggrek
        $this->assertEquals($kosC->id, $resNama->json('data.1.id')); // Kos Mawar
        $this->assertEquals($kosA->id, $resNama->json('data.2.id')); // Kos Melati
    }

    public function test_owner_active_tenants_pagination_and_sorting(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id]);

        $tenant1 = Pengguna::factory()->create(['nama' => 'Zaskia']);
        $tenant2 = Pengguna::factory()->create(['nama' => 'Budi']);
        $tenant3 = Pengguna::factory()->create(['nama' => 'Andi']);

        $booking1 = Pemesanan::factory()->create([
            'pengguna_id' => $tenant1->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'aktif',
            'tanggal_pemesanan' => now()->subMonths(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(20)->toDateString(),
        ]);

        $booking2 = Pemesanan::factory()->create([
            'pengguna_id' => $tenant2->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'aktif',
            'tanggal_pemesanan' => now()->subMonth()->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
        ]);

        $booking3 = Pemesanan::factory()->create([
            'pengguna_id' => $tenant3->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'status' => 'siap_serah_terima',
            'tanggal_pemesanan' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
        ]);

        Sanctum::actingAs($owner);

        // 1. Pagination & summary
        $res = $this->getJson('/api/v1/pemilik/penghuni?per_page=2&page=1');
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('summary.total_penghuni', 2)
            ->assertJsonPath('summary.total_siap_masuk', 1);

        // 2. Sort sisa_hari_asc (default, booking2 ends in 5 days)
        $resSisaHari = $this->getJson('/api/v1/pemilik/penghuni?sort=sisa_hari_asc');
        $resSisaHari->assertStatus(200);
        $this->assertEquals($booking2->id, $resSisaHari->json('data.0.id'));

        // 3. Sort nama_asc
        $resNama = $this->getJson('/api/v1/pemilik/penghuni?sort=nama_asc');
        $resNama->assertStatus(200);
        $this->assertEquals($booking3->id, $resNama->json('data.0.id')); // Andi
        $this->assertEquals($booking2->id, $resNama->json('data.1.id')); // Budi
        $this->assertEquals($booking1->id, $resNama->json('data.2.id')); // Zaskia
    }

    public function test_owner_wallet_mutations_pagination_and_sorting(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        Sanctum::actingAs($owner);

        // Mutasi saldo
        MutasiSaldoMitra::create([
            'pengguna_id' => $owner->id,
            'nominal' => 100000,
            'saldo_akhir' => 100000,
            'tipe' => 'kredit',
            'deskripsi' => 'Pendapatan sewa',
        ]);
        MutasiSaldoMitra::create([
            'pengguna_id' => $owner->id,
            'nominal' => 500000,
            'saldo_akhir' => 600000,
            'tipe' => 'kredit',
            'deskripsi' => 'Pendapatan sewa 2',
        ]);
        MutasiSaldoMitra::create([
            'pengguna_id' => $owner->id,
            'nominal' => 200000,
            'saldo_akhir' => 400000,
            'tipe' => 'debit',
            'deskripsi' => 'Penarikan dana',
        ]);

        $resMut = $this->getJson('/api/v1/wallet/mutations?per_page=2&page=1');
        $resMut->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3);

        $resMutFilter = $this->getJson('/api/v1/wallet/mutations?tipe=debit');
        $resMutFilter->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.tipe', 'debit');

        $resMutSort = $this->getJson('/api/v1/wallet/mutations?sort=nominal_desc');
        $resMutSort->assertStatus(200)
            ->assertJsonPath('data.0.nominal', 500000);
    }

    public function test_admin_user_pagination_and_sorting(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $userA = Pengguna::factory()->pemilik()->create(['nama' => 'Andi Mitra']);
        $userB = Pengguna::factory()->create(['nama' => 'Budi Penyewa', 'role' => 'pencari']);
        $userC = Pengguna::factory()->pemilik()->create(['nama' => 'Citra Mitra']);

        // userC has 3 kos
        Kos::factory()->count(3)->create(['pemilik_id' => $userC->id]);
        // userA has 1 kos
        Kos::factory()->create(['pemilik_id' => $userA->id]);

        // 1. Pagination
        $res = $this->getJson('/api/v1/admin/users?per_page=2&page=1');
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 4); // admin + userA + userB + userC

        // 2. Sort nama_asc
        $resNama = $this->getJson('/api/v1/admin/users?sort=nama_asc');
        $resNama->assertStatus(200);
        $names = collect($resNama->json('data'))->pluck('nama')->values()->all();
        $sortedNames = $names;
        sort($sortedNames);
        $this->assertEquals($sortedNames, $names);

        // 3. Sort unit_terbanyak
        $resUnit = $this->getJson('/api/v1/admin/users?role=pemilik&sort=unit_terbanyak');
        $resUnit->assertStatus(200);
        $this->assertEquals($userC->id, $resUnit->json('data.0.id'));
        $this->assertEquals($userA->id, $resUnit->json('data.1.id'));
    }

    public function test_admin_kos_pagination_and_sorting(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $owner = Pengguna::factory()->pemilik()->create();

        $kosCheap = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Murah',
            'harga' => 350000,
        ]);
        Kamar::factory()->count(2)->create(['kos_id' => $kosCheap->id]);

        $kosExpensive = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Mewah',
            'harga' => 2500000,
        ]);
        Kamar::factory()->count(10)->create(['kos_id' => $kosExpensive->id]);

        $kosMedium = Kos::factory()->create([
            'pemilik_id' => $owner->id,
            'nama' => 'Kos Nyaman',
            'harga' => 900000,
        ]);
        Kamar::factory()->count(5)->create(['kos_id' => $kosMedium->id]);

        // 1. Pagination
        $res = $this->getJson('/api/v1/admin/kos?per_page=2&page=1');
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3);

        // 2. Sort harga_asc
        $resHargaAsc = $this->getJson('/api/v1/admin/kos?sort=harga_asc');
        $resHargaAsc->assertStatus(200);
        $this->assertEquals($kosCheap->id, $resHargaAsc->json('data.0.id'));
        $this->assertEquals($kosExpensive->id, $resHargaAsc->json('data.2.id'));

        // 3. Sort kamar_banyak
        $resKamar = $this->getJson('/api/v1/admin/kos?sort=kamar_banyak');
        $resKamar->assertStatus(200);
        $this->assertEquals($kosExpensive->id, $resKamar->json('data.0.id'));
        $this->assertEquals($kosMedium->id, $resKamar->json('data.1.id'));
        $this->assertEquals($kosCheap->id, $resKamar->json('data.2.id'));
    }

    public function test_admin_verification_pagination_and_sorting(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $owner1 = Pengguna::factory()->pemilik()->create([
            'nama' => 'Bambang',
            'status_verifikasi' => 'pending',
            'updated_at' => now()->subDay(),
        ]);
        $owner2 = Pengguna::factory()->pemilik()->create([
            'nama' => 'Anto',
            'status_verifikasi' => 'verified',
            'updated_at' => now()->subDays(2),
        ]);
        $owner3 = Pengguna::factory()->pemilik()->create([
            'nama' => 'Zainal',
            'status_verifikasi' => 'rejected',
            'updated_at' => now(),
        ]);

        $res = $this->getJson('/api/v1/admin/verifications?per_page=2&page=1');
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('counts.pending', 1)
            ->assertJsonPath('counts.verified', 1)
            ->assertJsonPath('counts.rejected', 1)
            ->assertJsonPath('counts.total', 3);

        // Sort nama_asc
        $resNama = $this->getJson('/api/v1/admin/verifications?sort=nama_asc');
        $resNama->assertStatus(200);
        $this->assertEquals($owner2->id, $resNama->json('data.0.id')); // Anto
        $this->assertEquals($owner1->id, $resNama->json('data.1.id')); // Bambang
        $this->assertEquals($owner3->id, $resNama->json('data.2.id')); // Zainal
    }

    public function test_admin_withdrawal_pagination_and_sorting(): void
    {
        $admin = Pengguna::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $owner = Pengguna::factory()->pemilik()->create();
        $bank = RekeningBankMitra::create([
            'pengguna_id' => $owner->id,
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'nama_pemilik' => $owner->nama,
            'is_utama' => true,
        ]);

        $wd1 = PenarikanDana::create([
            'kode_penarikan' => 'WD-001',
            'pengguna_id' => $owner->id,
            'rekening_bank_id' => $bank->id,
            'nominal' => 200000,
            'status' => 'menunggu',
        ]);
        $wd2 = PenarikanDana::create([
            'kode_penarikan' => 'WD-002',
            'pengguna_id' => $owner->id,
            'rekening_bank_id' => $bank->id,
            'nominal' => 1500000,
            'status' => 'selesai',
        ]);
        $wd3 = PenarikanDana::create([
            'kode_penarikan' => 'WD-003',
            'pengguna_id' => $owner->id,
            'rekening_bank_id' => $bank->id,
            'nominal' => 500000,
            'status' => 'ditolak',
        ]);

        $res = $this->getJson('/api/v1/admin/withdrawals?per_page=2&page=1');
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('counts.menunggu', 1)
            ->assertJsonPath('counts.selesai', 1)
            ->assertJsonPath('counts.ditolak', 1);

        // Sort nominal_desc
        $resNominal = $this->getJson('/api/v1/admin/withdrawals?sort=nominal_desc');
        $resNominal->assertStatus(200);
        $this->assertEquals($wd2->id, $resNominal->json('data.0.id')); // 1.5jt
        $this->assertEquals($wd3->id, $resNominal->json('data.1.id')); // 500rb
        $this->assertEquals($wd1->id, $resNominal->json('data.2.id')); // 200rb
    }

    public function test_kos_reviews_pagination_and_sorting(): void
    {
        $owner = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $owner->id]);

        $tenant1 = Pengguna::factory()->create();
        $tenant2 = Pengguna::factory()->create();
        $tenant3 = Pengguna::factory()->create();

        $rev1 = Ulasan::create([
            'kos_id' => $kos->id,
            'pengguna_id' => $tenant1->id,
            'rating' => 2,
            'komentar' => 'Kurang bersih',
            'created_at' => now()->subDays(5),
        ]);
        $rev2 = Ulasan::create([
            'kos_id' => $kos->id,
            'pengguna_id' => $tenant2->id,
            'rating' => 5,
            'komentar' => 'Sangat nyaman sekali',
            'created_at' => now()->subDays(2),
        ]);
        $rev3 = Ulasan::create([
            'kos_id' => $kos->id,
            'pengguna_id' => $tenant3->id,
            'rating' => 4,
            'komentar' => 'Fasilitas lumayan lengkap',
            'created_at' => now()->subDay(),
        ]);

        // 1. Pagination (per_page 2)
        $res = $this->getJson("/api/v1/kos/{$kos->id}/reviews?per_page=2&page=1");
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.last_page', 2);

        // 2. Sort rating_desc
        $resRatingDesc = $this->getJson("/api/v1/kos/{$kos->id}/reviews?sort=rating_desc");
        $resRatingDesc->assertStatus(200);
        $this->assertEquals($rev2->id, $resRatingDesc->json('data.0.id')); // rating 5
        $this->assertEquals($rev3->id, $resRatingDesc->json('data.1.id')); // rating 4
        $this->assertEquals($rev1->id, $resRatingDesc->json('data.2.id')); // rating 2

        // 3. Sort rating_asc
        $resRatingAsc = $this->getJson("/api/v1/kos/{$kos->id}/reviews?sort=rating_asc");
        $resRatingAsc->assertStatus(200);
        $this->assertEquals($rev1->id, $resRatingAsc->json('data.0.id')); // rating 2
    }
}
