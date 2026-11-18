<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Generate Snap token for DP (20%) payment.
     */
    public function checkoutDp(Request $request, int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['kos.pemilik', 'kamar'])->findOrFail($id);

        try {
            $transaksi = $this->paymentService->checkoutDp($pemesanan, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Token transaksi pembayaran DP berhasil dibuat.',
                'data' => [
                    'kode_transaksi' => $transaksi->kode_transaksi,
                    'tipe' => $transaksi->tipe,
                    'nominal_pokok' => $transaksi->nominal_pokok,
                    'biaya_admin' => $transaksi->biaya_admin,
                    'total_tagihan' => $transaksi->total_tagihan,
                    'snap_token' => $transaksi->snap_token,
                    'snap_redirect_url' => $transaksi->snap_redirect_url,
                    'status' => $transaksi->status,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Generate Snap token for Pelunasan (80%) payment.
     */
    public function checkoutPelunasan(Request $request, int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['kos.pemilik', 'kamar'])->findOrFail($id);

        try {
            $transaksi = $this->paymentService->checkoutPelunasan($pemesanan, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Token transaksi pelunasan sewa berhasil dibuat.',
                'data' => [
                    'kode_transaksi' => $transaksi->kode_transaksi,
                    'tipe' => $transaksi->tipe,
                    'nominal_pokok' => $transaksi->nominal_pokok,
                    'biaya_admin' => $transaksi->biaya_admin,
                    'total_tagihan' => $transaksi->total_tagihan,
                    'snap_token' => $transaksi->snap_token,
                    'snap_redirect_url' => $transaksi->snap_redirect_url,
                    'status' => $transaksi->status,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Handover key: Confirm physical key has been handed over to tenant.
     */
    public function handoverKey(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $pemesanan = Pemesanan::with(['kos.pemilik', 'kamar'])->findOrFail($id);

        try {
            $updated = $this->paymentService->confirmKeyHandover($pemesanan, $request->user(), $validated['catatan'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Serah terima kunci berhasil dikonfirmasi. Kamar resmi aktif dihuni dan dana sewa telah masuk ke saldo mitra.',
                'data' => $updated,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Check payment status of a booking.
     */
    public function checkStatus(Request $request, int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['transaksiDp', 'transaksiPelunasan'])->findOrFail($id);

        $user = $request->user();
        if ($pemesanan->pengguna_id !== $user->id && $pemesanan->kos?->pemilik_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status_pemesanan' => $pemesanan->status,
                'status_pembayaran' => $pemesanan->status_pembayaran,
                'transaksi_dp' => $pemesanan->transaksiDp,
                'transaksi_pelunasan' => $pemesanan->transaksiPelunasan,
            ],
        ]);
    }

    /**
     * Synchronize payment status directly with Midtrans REST API.
     */
    public function syncStatus(Request $request, int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['kos.pemilik', 'kamar', 'transaksiDp', 'transaksiPelunasan'])->findOrFail($id);

        $user = $request->user();
        if ($pemesanan->pengguna_id !== $user->id && $pemesanan->kos?->pemilik_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        try {
            $synced = $this->paymentService->syncPaymentStatus($pemesanan);

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil disinkronkan.',
                'data' => [
                    'status_pemesanan' => $synced->status,
                    'status_pembayaran' => $synced->status_pembayaran,
                    'transaksi_dp' => $synced->transaksiDp,
                    'transaksi_pelunasan' => $synced->transaksiPelunasan,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
