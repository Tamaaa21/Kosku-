<?php

namespace App\Services;

use App\Models\Pemesanan;
use App\Models\Pengguna;
use App\Models\TransaksiPembayaran;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        protected MidtransService $midtransService,
        protected WalletService $walletService
    ) {}

    /**
     * Create or retrieve Snap checkout for DP (Down Payment).
     */
    public function checkoutDp(Pemesanan $pemesanan, Pengguna $user): TransaksiPembayaran
    {
        if ($pemesanan->pengguna_id !== $user->id) {
            throw new Exception('Anda tidak memiliki akses untuk pemesanan ini.');
        }

        if ($pemesanan->status_pembayaran === 'dp_dibayar' || $pemesanan->status_pembayaran === 'lunas') {
            throw new Exception('Uang muka (DP) untuk pemesanan ini sudah lunas.');
        }

        // Calculate and enforce valid nominal DP
        $totalHarga = (int) $pemesanan->total_harga;
        $dpPercent = (int) ($pemesanan->persentase_dp ?: config('midtrans.dp_percentage', 20));
        $nominalPokok = (int) ($pemesanan->nominal_dp ?: round(($totalHarga * $dpPercent) / 100));

        // Always save calculations if missing
        if (! $pemesanan->nominal_dp || ! $pemesanan->sisa_pelunasan) {
            $sisa = $totalHarga - $nominalPokok;
            $komisiPersen = (int) ($pemesanan->komisi_persen ?: config('midtrans.commission_percentage', 5));
            $komisiNominal = (int) round(($totalHarga * $komisiPersen) / 100);
            $pemesanan->update([
                'persentase_dp' => $dpPercent,
                'nominal_dp' => $nominalPokok,
                'sisa_pelunasan' => $sisa,
                'komisi_persen' => $komisiPersen,
                'komisi_nominal' => $komisiNominal,
                'total_hak_mitra' => $totalHarga - $komisiNominal,
            ]);
        }

        // Reuse recent pending transaction if still valid within last 15 minutes and nominal matches
        $existing = TransaksiPembayaran::where('pemesanan_id', $pemesanan->id)
            ->where('tipe', 'dp')
            ->where('status', 'pending')
            ->where('nominal_pokok', $nominalPokok)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->latest('id')
            ->first();

        if ($existing && $existing->snap_token) {
            return $existing;
        }

        $biayaAdmin = (int) config('midtrans.gateway_fee', 4000);
        $totalTagihan = $nominalPokok + $biayaAdmin;
        $orderId = 'KST-DP-'.$pemesanan->id.'-'.time();

        $customerDetails = [
            'first_name' => $user->nama,
            'email' => $user->email,
            'phone' => $user->telepon ?? '081234567890',
        ];

        $itemDetails = [
            [
                'id' => 'DP-'.$pemesanan->id,
                'price' => $nominalPokok,
                'quantity' => 1,
                'name' => 'DP Sewa (20%) - '.$pemesanan->kos->nama,
            ],
            [
                'id' => 'FEE-GATEWAY',
                'price' => $biayaAdmin,
                'quantity' => 1,
                'name' => 'Biaya Layanan Pembayaran',
            ],
        ];

        $snap = $this->midtransService->createSnapTransaction($orderId, $totalTagihan, $customerDetails, $itemDetails);

        return TransaksiPembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'kode_transaksi' => $orderId,
            'tipe' => 'dp',
            'nominal_pokok' => $nominalPokok,
            'biaya_admin' => $biayaAdmin,
            'total_tagihan' => $totalTagihan,
            'snap_token' => $snap['token'],
            'snap_redirect_url' => $snap['redirect_url'],
            'status' => 'pending',
        ]);
    }

    /**
     * Create or retrieve Snap checkout for Pelunasan (80%).
     */
    public function checkoutPelunasan(Pemesanan $pemesanan, Pengguna $user): TransaksiPembayaran
    {
        if ($pemesanan->pengguna_id !== $user->id) {
            throw new Exception('Anda tidak memiliki akses untuk pemesanan ini.');
        }

        if ($pemesanan->status_pembayaran !== 'dp_dibayar') {
            throw new Exception('Uang muka (DP) harus dilunasi terlebih dahulu sebelum melakukan pelunasan sewa.');
        }

        if ($pemesanan->status_pembayaran === 'lunas') {
            throw new Exception('Pemesanan ini sudah lunas sepenuhnya.');
        }

        $nominalPokok = (int) ($pemesanan->sisa_pelunasan ?: ($pemesanan->total_harga - $pemesanan->nominal_dp));

        // Reuse recent pending transaction if still valid within last 15 minutes and nominal matches
        $existing = TransaksiPembayaran::where('pemesanan_id', $pemesanan->id)
            ->where('tipe', 'pelunasan')
            ->where('status', 'pending')
            ->where('nominal_pokok', $nominalPokok)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->latest('id')
            ->first();

        if ($existing && $existing->snap_token) {
            return $existing;
        }

        $biayaAdmin = (int) config('midtrans.gateway_fee', 4000);
        $totalTagihan = $nominalPokok + $biayaAdmin;
        $orderId = 'KST-LUNAS-'.$pemesanan->id.'-'.time();

        $customerDetails = [
            'first_name' => $user->nama,
            'email' => $user->email,
            'phone' => $user->telepon ?? '081234567890',
        ];

        $itemDetails = [
            [
                'id' => 'LUNAS-'.$pemesanan->id,
                'price' => $nominalPokok,
                'quantity' => 1,
                'name' => 'Pelunasan Sewa (80%) - '.$pemesanan->kos->nama,
            ],
            [
                'id' => 'FEE-GATEWAY',
                'price' => $biayaAdmin,
                'quantity' => 1,
                'name' => 'Biaya Layanan Pembayaran',
            ],
        ];

        $snap = $this->midtransService->createSnapTransaction($orderId, $totalTagihan, $customerDetails, $itemDetails);

        return TransaksiPembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'kode_transaksi' => $orderId,
            'tipe' => 'pelunasan',
            'nominal_pokok' => $nominalPokok,
            'biaya_admin' => $biayaAdmin,
            'total_tagihan' => $totalTagihan,
            'snap_token' => $snap['token'],
            'snap_redirect_url' => $snap['redirect_url'],
            'status' => 'pending',
        ]);
    }

    /**
     * Query Midtrans API directly to synchronize payment status in real-time.
     */
    public function syncPaymentStatus(Pemesanan $pemesanan): Pemesanan
    {
        $latestTransaksi = TransaksiPembayaran::where('pemesanan_id', $pemesanan->id)
            ->latest('id')
            ->first();

        if (! $latestTransaksi) {
            return $pemesanan;
        }

        $midtransStatus = $this->midtransService->getTransactionStatus($latestTransaksi->kode_transaksi);

        if ($midtransStatus) {
            $transactionStatus = (string) ($midtransStatus['transaction_status'] ?? '');
            $fraudStatus = (string) ($midtransStatus['fraud_status'] ?? '');
            $paymentType = (string) ($midtransStatus['payment_type'] ?? '');

            $isSettled = in_array($transactionStatus, ['settlement', 'capture'], true)
                && ($transactionStatus !== 'capture' || $fraudStatus === 'accept');

            if ($isSettled) {
                $latestTransaksi->update([
                    'status' => 'settlement',
                    'metode_pembayaran' => $paymentType,
                    'dibayar_pada' => now(),
                    'payload_callback' => $midtransStatus,
                ]);

                if ($latestTransaksi->tipe === 'dp') {
                    $pemesanan->update([
                        'status_pembayaran' => 'dp_dibayar',
                        'status' => 'dp_dibayar',
                    ]);

                    if ($pemesanan->kamar) {
                        $pemesanan->kamar->update(['status' => 'terisi']);
                    }
                } elseif ($latestTransaksi->tipe === 'pelunasan') {
                    $pemesanan->update([
                        'status_pembayaran' => 'lunas',
                        'status' => 'siap_serah_terima',
                    ]);
                }
            } elseif ($transactionStatus === 'pending') {
                $latestTransaksi->update([
                    'status' => 'pending',
                    'metode_pembayaran' => $paymentType,
                    'payload_callback' => $midtransStatus,
                ]);
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
                $latestTransaksi->update([
                    'status' => $transactionStatus,
                    'payload_callback' => $midtransStatus,
                ]);

                if ($latestTransaksi->tipe === 'dp' && $pemesanan->status_pembayaran === 'belum_dibayar') {
                    $pemesanan->update(['status' => $transactionStatus === 'expire' ? 'kedaluwarsa' : 'dibatalkan']);
                    if ($pemesanan->kamar) {
                        $pemesanan->kamar->update(['status' => 'tersedia']);
                    }
                }
            }
        }

        return $pemesanan->fresh(['pengguna', 'kos.pemilik', 'kamar', 'transaksiDp', 'transaksiPelunasan']);
    }

    /**
     * Process Midtrans webhook callback notification.
     */
    public function handleWebhook(array $payload): array
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');
        $paymentType = (string) ($payload['payment_type'] ?? '');

        if (! $this->midtransService->isValidSignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans Webhook: Invalid Signature', ['order_id' => $orderId]);
            throw new Exception('Invalid signature key.');
        }

        return DB::transaction(function () use ($orderId, $transactionStatus, $fraudStatus, $paymentType, $payload) {
            /** @var TransaksiPembayaran|null $transaksi */
            $transaksi = TransaksiPembayaran::where('kode_transaksi', $orderId)->lockForUpdate()->first();

            if (! $transaksi) {
                Log::warning('Midtrans Webhook: Transaction not found', ['order_id' => $orderId]);
                throw new Exception("Transaksi {$orderId} tidak ditemukan.");
            }

            /** @var Pemesanan $pemesanan */
            $pemesanan = Pemesanan::with(['kamar', 'kos.pemilik'])->where('id', $transaksi->pemesanan_id)->lockForUpdate()->first();

            $isSettled = in_array($transactionStatus, ['settlement', 'capture'], true)
                && ($transactionStatus !== 'capture' || $fraudStatus === 'accept');

            if ($isSettled) {
                $transaksi->update([
                    'status' => 'settlement',
                    'metode_pembayaran' => $paymentType,
                    'dibayar_pada' => now(),
                    'payload_callback' => $payload,
                ]);

                if ($transaksi->tipe === 'dp') {
                    $pemesanan->update([
                        'status_pembayaran' => 'dp_dibayar',
                        'status' => 'dp_dibayar',
                    ]);

                    // Lock the room so it's marked as occupied
                    if ($pemesanan->kamar) {
                        $pemesanan->kamar->update(['status' => 'terisi']);
                    }
                } elseif ($transaksi->tipe === 'pelunasan') {
                    $pemesanan->update([
                        'status_pembayaran' => 'lunas',
                        'status' => 'siap_serah_terima',
                    ]);
                }

                return [
                    'status' => 'success',
                    'message' => "Pembayaran {$transaksi->tipe} berhasil dikonfirmasi.",
                ];
            }

            if ($transactionStatus === 'pending') {
                $transaksi->update([
                    'status' => 'pending',
                    'metode_pembayaran' => $paymentType,
                    'payload_callback' => $payload,
                ]);

                return [
                    'status' => 'pending',
                    'message' => 'Pembayaran sedang menunggu penyelesaian.',
                ];
            }

            if (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
                $transaksi->update([
                    'status' => $transactionStatus,
                    'payload_callback' => $payload,
                ]);

                if ($transaksi->tipe === 'dp' && $pemesanan->status_pembayaran === 'belum_dibayar') {
                    $pemesanan->update([
                        'status' => $transactionStatus === 'expire' ? 'kedaluwarsa' : 'dibatalkan',
                    ]);

                    // Release room back to available
                    if ($pemesanan->kamar) {
                        $pemesanan->kamar->update(['status' => 'tersedia']);
                    }
                }

                return [
                    'status' => $transactionStatus,
                    'message' => "Transaksi {$transactionStatus}.",
                ];
            }

            return [
                'status' => 'ignored',
                'message' => 'Status transaksi tidak memerlukan aksi.',
            ];
        });
    }

    /**
     * Handover key: Confirm key received, activate rental, and credit owner wallet.
     */
    public function confirmKeyHandover(Pemesanan $pemesanan, Pengguna $user, ?string $catatan = null): Pemesanan
    {
        $isPemilik = $pemesanan->kos && $pemesanan->kos->pemilik_id === $user->id;
        $isPencari = $pemesanan->pengguna_id === $user->id;
        $isAdmin = $user->role === 'admin';

        if (! $isPemilik && ! $isPencari && ! $isAdmin) {
            throw new Exception('Anda tidak berwenang mengonfirmasi serah terima kunci untuk pemesanan ini.');
        }

        if ($pemesanan->status_pembayaran !== 'lunas') {
            throw new Exception('Serah terima kunci hanya dapat dilakukan setelah seluruh biaya sewa (pelunasan) diselesaikan.');
        }

        if ($pemesanan->status === 'aktif') {
            return $pemesanan;
        }

        return DB::transaction(function () use ($pemesanan, $catatan) {
            $pemesanan->update([
                'status' => 'aktif',
                'kunci_diserahkan_at' => now(),
                'catatan_serah_terima' => $catatan,
            ]);

            // Release clean funds to owner wallet if not credited yet
            if (! $pemesanan->mutasiSaldo()->exists()) {
                $pemilik = $pemesanan->kos->pemilik;
                if ($pemilik) {
                    $keterangan = "Pendapatan sewa kamar {$pemesanan->kamar->nama_kamar} - {$pemesanan->kos->nama} (Pesanan #{$pemesanan->id})";
                    $this->walletService->credit($pemilik, (int) $pemesanan->total_hak_mitra, $keterangan, $pemesanan);
                }
            }

            return $pemesanan->fresh(['pengguna', 'kos.pemilik', 'kamar', 'mutasiSaldo']);
        });
    }
}
