<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use App\Models\TransaksiPembayaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentAndWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_pencari_can_checkout_dp_and_receive_snap_token(): void
    {
        Http::fake([
            '*/snap/v1/transactions' => Http::response([
                'token' => 'dummy-snap-token-12345',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/dummy-12345',
            ], 201),
        ]);

        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id, 'harga' => 1000000]);

        $pencari = Pengguna::factory()->create(['role' => 'pencari']);
        Sanctum::actingAs($pencari);

        // 1. Create booking
        $resBooking = $this->postJson('/api/v1/bookings', [
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->addDays(2)->toDateString(),
            'durasi_tipe' => 'bulanan',
            'durasi_jumlah' => 1,
        ]);
        $resBooking->assertStatus(201);
        $bookingId = $resBooking->json('data.id');

        // Check DP fields calculation: Total 1,000,000 => DP 20% = 200,000, Pelunasan = 800,000, Commission 5% = 50,000
        $this->assertDatabaseHas('pemesanan', [
            'id' => $bookingId,
            'total_harga' => 1000000,
            'nominal_dp' => 200000,
            'sisa_pelunasan' => 800000,
            'komisi_nominal' => 50000,
            'total_hak_mitra' => 950000,
            'status_pembayaran' => 'belum_dibayar',
        ]);

        // 2. Checkout DP
        $resCheckout = $this->postJson("/api/v1/bookings/{$bookingId}/checkout-dp");
        $resCheckout->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.snap_token', 'dummy-snap-token-12345')
            ->assertJsonPath('data.tipe', 'dp')
            ->assertJsonPath('data.nominal_pokok', 200000)
            ->assertJsonPath('data.total_tagihan', 204000); // 200000 + 4000 admin fee

        $this->assertDatabaseHas('transaksi_pembayaran', [
            'pemesanan_id' => $bookingId,
            'tipe' => 'dp',
            'snap_token' => 'dummy-snap-token-12345',
            'status' => 'pending',
        ]);
    }

    public function test_midtrans_webhook_settlement_updates_dp_status_and_locks_room(): void
    {
        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->tersedia()->create(['kos_id' => $kos->id, 'harga' => 1000000]);

        $pencari = Pengguna::factory()->create(['role' => 'pencari']);

        $pemesanan = Pemesanan::create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->toDateString(),
            'total_harga' => 1000000,
            'nominal_dp' => 200000,
            'sisa_pelunasan' => 800000,
            'komisi_nominal' => 50000,
            'total_hak_mitra' => 950000,
            'status' => 'menunggu',
            'status_pembayaran' => 'belum_dibayar',
        ]);

        $orderId = 'KST-DP-'.$pemesanan->id.'-'.time();
        $grossAmount = '204000';
        $statusCode = '200';

        TransaksiPembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'kode_transaksi' => $orderId,
            'tipe' => 'dp',
            'nominal_pokok' => 200000,
            'biaya_admin' => 4000,
            'total_tagihan' => 204000,
            'status' => 'pending',
        ]);

        $serverKey = config('midtrans.server_key');
        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        $response = $this->postJson('/api/v1/webhooks/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('transaksi_pembayaran', [
            'kode_transaksi' => $orderId,
            'status' => 'settlement',
            'metode_pembayaran' => 'qris',
        ]);

        $this->assertDatabaseHas('pemesanan', [
            'id' => $pemesanan->id,
            'status' => 'dp_dibayar',
            'status_pembayaran' => 'dp_dibayar',
        ]);

        // Room should now be locked/terisi
        $this->assertDatabaseHas('kamar', [
            'id' => $kamar->id,
            'status' => 'terisi',
        ]);
    }

    public function test_midtrans_webhook_with_invalid_signature_is_rejected(): void
    {
        $response = $this->postJson('/api/v1/webhooks/midtrans', [
            'order_id' => 'KST-FAKE-123',
            'status_code' => '200',
            'gross_amount' => '100000',
            'signature_key' => 'invalid-signature-hash',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_pencari_can_checkout_pelunasan_and_confirm_handover_key(): void
    {
        Http::fake([
            '*/snap/v1/transactions' => Http::response([
                'token' => 'dummy-lunas-token-999',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/dummy-999',
            ], 201),
        ]);

        $pemilik = Pengguna::factory()->pemilik()->create();
        $kos = Kos::factory()->create(['pemilik_id' => $pemilik->id]);
        $kamar = Kamar::factory()->create(['kos_id' => $kos->id, 'harga' => 1000000, 'status' => 'terisi']);

        $pencari = Pengguna::factory()->create(['role' => 'pencari']);

        // Pemesanan with DP already paid
        $pemesanan = Pemesanan::create([
            'pengguna_id' => $pencari->id,
            'kos_id' => $kos->id,
            'kamar_id' => $kamar->id,
            'tanggal_pemesanan' => now()->toDateString(),
            'total_harga' => 1000000,
            'nominal_dp' => 200000,
            'sisa_pelunasan' => 800000,
            'komisi_nominal' => 50000,
            'total_hak_mitra' => 950000,
            'status' => 'dp_dibayar',
            'status_pembayaran' => 'dp_dibayar',
        ]);

        Sanctum::actingAs($pencari);

        // 1. Checkout pelunasan
        $resPelunasan = $this->postJson("/api/v1/bookings/{$pemesanan->id}/checkout-pelunasan");
        $resPelunasan->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.tipe', 'pelunasan')
            ->assertJsonPath('data.nominal_pokok', 800000);

        $orderId = $resPelunasan->json('data.kode_transaksi');
        $statusCode = '200';
        $grossAmount = '804000';
        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.config('midtrans.server_key'));

        // 2. Webhook confirms pelunasan
        $resWebhook = $this->postJson('/api/v1/webhooks/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ]);
        $resWebhook->assertStatus(200);

        $this->assertDatabaseHas('pemesanan', [
            'id' => $pemesanan->id,
            'status' => 'siap_serah_terima',
            'status_pembayaran' => 'lunas',
        ]);

        // 3. Serah terima kunci (confirm by pemilik)
        Sanctum::actingAs($pemilik);
        $resHandover = $this->postJson("/api/v1/bookings/{$pemesanan->id}/handover-key", [
            'catatan' => 'Kunci kamar nomor 01 telah diserahkan langsung ke penyewa.',
        ]);

        $resHandover->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pemesanan', [
            'id' => $pemesanan->id,
            'status' => 'aktif',
        ]);

        // Hak mitra (950,000) should be credited to owner wallet
        $this->assertDatabaseHas('mutasi_saldo_mitra', [
            'pengguna_id' => $pemilik->id,
            'pemesanan_id' => $pemesanan->id,
            'tipe' => 'kredit',
            'nominal' => 950000,
            'saldo_akhir' => 950000,
        ]);

        $this->assertEquals(950000, $pemilik->saldoTersedia());
    }
}
