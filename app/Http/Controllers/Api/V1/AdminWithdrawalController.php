<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminWithdrawalController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * List all withdrawal requests across the platform.
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $query = PenarikanDana::with(['pengguna:id,nama,email,telepon', 'rekeningBank', 'admin:id,nama']);

        if ($status && in_array($status, ['menunggu', 'diproses', 'selesai', 'ditolak'], true)) {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('kode_penarikan', 'like', "%{$keyword}%")
                    ->orWhereHas('pengguna', function ($userQuery) use ($keyword) {
                        $userQuery->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%")
                            ->orWhere('telepon', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('rekeningBank', function ($bankQuery) use ($keyword) {
                        $bankQuery->where('nomor_rekening', 'like', "%{$keyword}%")
                            ->orWhere('nama_pemilik', 'like', "%{$keyword}%")
                            ->orWhere('nama_bank', 'like', "%{$keyword}%");
                    });
            });
        }

        match ($request->input('sort')) {
            'oldest' => $query->oldest('id'),
            'nominal_desc' => $query->orderBy('nominal', 'desc'),
            'nominal_asc' => $query->orderBy('nominal', 'asc'),
            default => $query->latest('id'),
        };

        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $withdrawals = $query->paginate($perPage);

        $counts = [
            'menunggu' => PenarikanDana::where('status', 'menunggu')->count(),
            'selesai' => PenarikanDana::where('status', 'selesai')->count(),
            'ditolak' => PenarikanDana::where('status', 'ditolak')->count(),
            'total' => PenarikanDana::count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $withdrawals->items(),
            'counts' => $counts,
            'meta' => [
                'current_page' => $withdrawals->currentPage(),
                'last_page' => $withdrawals->lastPage(),
                'per_page' => $withdrawals->perPage(),
                'total' => $withdrawals->total(),
                'from' => $withdrawals->firstItem(),
                'to' => $withdrawals->lastItem(),
            ],
        ]);
    }

    /**
     * Approve and mark withdrawal as completed with transfer proof.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $penarikan = PenarikanDana::with(['pengguna', 'rekeningBank'])->findOrFail($id);

        if ($penarikan->status === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Penarikan ini sudah disetujui sebelumnya.',
            ], 422);
        }

        if ($penarikan->status === 'ditolak') {
            return response()->json([
                'success' => false,
                'message' => 'Penarikan yang sudah ditolak tidak dapat disetujui kembali.',
            ], 422);
        }

        $validated = $request->validate([
            'bukti_transfer' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:5120',
            'bukti_transfer_ref' => 'nullable|string|max:255',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');
            $buktiPath = Storage::url($path);
        } elseif (! empty($validated['bukti_transfer_ref'])) {
            $buktiPath = $validated['bukti_transfer_ref'];
        }

        $penarikan->update([
            'status' => 'selesai',
            'bukti_transfer' => $buktiPath ?? $penarikan->bukti_transfer,
            'catatan_admin' => $validated['catatan_admin'] ?? null,
            'disetujui_oleh' => $request->user()->id,
            'diproses_pada' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pencairan dana berhasil disetujui dan diselesaikan.',
            'data' => $penarikan->fresh(['pengguna', 'rekeningBank', 'admin']),
        ]);
    }

    /**
     * Reject withdrawal and refund amount back to owner wallet.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $penarikan = PenarikanDana::with('pengguna')->findOrFail($id);

        if ($penarikan->status === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Penarikan yang sudah selesai tidak dapat ditolak.',
            ], 422);
        }

        if ($penarikan->status === 'ditolak') {
            return response()->json([
                'success' => false,
                'message' => 'Penarikan ini sudah dalam status ditolak.',
            ], 422);
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($penarikan, $validated, $request) {
                $penarikan->update([
                    'status' => 'ditolak',
                    'alasan_penolakan' => $validated['alasan_penolakan'],
                    'disetujui_oleh' => $request->user()->id,
                    'diproses_pada' => now(),
                ]);

                // Refund to owner's wallet
                $alasan = $validated['alasan_penolakan'];
                $deskripsi = "Pengembalian saldo penarikan ditolak ({$penarikan->kode_penarikan}): {$alasan}";
                $this->walletService->credit($penarikan->pengguna, (int) $penarikan->nominal, $deskripsi);
            });

            return response()->json([
                'success' => true,
                'message' => 'Penarikan dana berhasil ditolak dan saldo telah dikembalikan ke dompet mitra.',
                'data' => $penarikan->fresh(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
