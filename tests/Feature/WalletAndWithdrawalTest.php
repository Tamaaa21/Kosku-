<?php

namespace Tests\Feature;

use App\Models\MutasiSaldoMitra;
use App\Models\Pengguna;
use App\Models\RekeningBankMitra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WalletAndWithdrawalTest extends TestCase
{
    use RefreshDatabase;

    public function test_pemilik_can_manage_bank_accounts(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        Sanctum::actingAs($pemilik);

        // 1. Add bank account
        $resStore = $this->postJson('/api/v1/wallet/bank-accounts', [
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'nama_pemilik' => 'Budi Santoso',
            'is_utama' => true,
        ]);

        $resStore->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama_bank', 'BCA');

        $this->assertDatabaseHas('rekening_bank_mitra', [
            'pengguna_id' => $pemilik->id,
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'is_utama' => true,
        ]);

        // 2. List bank accounts
        $resList = $this->getJson('/api/v1/wallet/bank-accounts');
        $resList->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    public function test_pemilik_can_request_withdrawal_if_balance_sufficient(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $rekening = RekeningBankMitra::create([
            'pengguna_id' => $pemilik->id,
            'nama_bank' => 'MANDIRI',
            'nomor_rekening' => '987654321',
            'nama_pemilik' => $pemilik->nama,
            'is_utama' => true,
        ]);

        // Give initial balance of Rp 1,500,000
        MutasiSaldoMitra::create([
            'pengguna_id' => $pemilik->id,
            'tipe' => 'kredit',
            'nominal' => 1500000,
            'saldo_akhir' => 1500000,
            'deskripsi' => 'Saldo awal sewa',
        ]);

        Sanctum::actingAs($pemilik);

        // Check summary before withdrawal
        $resSummary = $this->getJson('/api/v1/wallet/summary');
        $resSummary->assertStatus(200)
            ->assertJsonPath('data.saldo_tersedia', 1500000);

        // Request withdrawal of Rp 500,000
        $resWd = $this->postJson('/api/v1/wallet/withdrawals', [
            'rekening_bank_id' => $rekening->id,
            'nominal' => 500000,
        ]);

        $resWd->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nominal', 500000)
            ->assertJsonPath('data.status', 'menunggu');

        // Remaining balance should now be Rp 1,000,000
        $this->assertEquals(1000000, $pemilik->saldoTersedia());

        $this->assertDatabaseHas('mutasi_saldo_mitra', [
            'pengguna_id' => $pemilik->id,
            'tipe' => 'debit',
            'nominal' => 500000,
            'saldo_akhir' => 1000000,
        ]);
    }

    public function test_pemilik_cannot_request_withdrawal_if_balance_insufficient(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $rekening = RekeningBankMitra::create([
            'pengguna_id' => $pemilik->id,
            'nama_bank' => 'BRI',
            'nomor_rekening' => '5551234',
            'nama_pemilik' => $pemilik->nama,
            'is_utama' => true,
        ]);

        Sanctum::actingAs($pemilik);

        // Owner has 0 balance, trying to withdraw Rp 100,000
        $res = $this->postJson('/api/v1/wallet/withdrawals', [
            'rekening_bank_id' => $rekening->id,
            'nominal' => 100000,
        ]);

        $res->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_admin_can_approve_and_reject_withdrawal(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $admin = Pengguna::factory()->admin()->create();

        $rekening = RekeningBankMitra::create([
            'pengguna_id' => $pemilik->id,
            'nama_bank' => 'BNI',
            'nomor_rekening' => '777888',
            'nama_pemilik' => $pemilik->nama,
            'is_utama' => true,
        ]);

        // 1. Initial credit 2,000,000
        MutasiSaldoMitra::create([
            'pengguna_id' => $pemilik->id,
            'tipe' => 'kredit',
            'nominal' => 2000000,
            'saldo_akhir' => 2000000,
            'deskripsi' => 'Saldo awal sewa',
        ]);

        // 2. Owner requests withdrawal of 500,000
        Sanctum::actingAs($pemilik);
        $resWd = $this->postJson('/api/v1/wallet/withdrawals', [
            'rekening_bank_id' => $rekening->id,
            'nominal' => 500000,
        ]);
        $wdId = $resWd->json('data.id');

        // 3. Admin rejects withdrawal with reason -> Balance must be refunded!
        Sanctum::actingAs($admin);
        $resReject = $this->postJson("/api/v1/admin/withdrawals/{$wdId}/reject", [
            'alasan_penolakan' => 'Nomor rekening bank tidak valid sesuai identitas.',
        ]);

        $resReject->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('penarikan_dana', [
            'id' => $wdId,
            'status' => 'ditolak',
        ]);

        // Balance should be back to 2,000,000!
        $this->assertEquals(2000000, $pemilik->saldoTersedia());

        // 4. Owner requests another withdrawal of 1,000,000
        Sanctum::actingAs($pemilik);
        $resWd2 = $this->postJson('/api/v1/wallet/withdrawals', [
            'rekening_bank_id' => $rekening->id,
            'nominal' => 1000000,
        ]);
        $wdId2 = $resWd2->json('data.id');

        // 5. Admin approves withdrawal
        Sanctum::actingAs($admin);
        $resApprove = $this->postJson("/api/v1/admin/withdrawals/{$wdId2}/approve", [
            'catatan_admin' => 'Transfer berhasil via BI-FAST Ref #TRX998811',
            'bukti_transfer_ref' => 'REF-BIFAST-998811',
        ]);

        $resApprove->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('penarikan_dana', [
            'id' => $wdId2,
            'status' => 'selesai',
            'disetujui_oleh' => $admin->id,
        ]);
    }
}
