<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Get owner balance and financial summary.
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $summary = $this->walletService->getSummary($user);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get owner wallet mutations history.
     */
    public function mutations(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $user->mutasiSaldo()->with(['pemesanan.kos', 'penarikanDana.rekeningBank']);

        if ($request->filled('tipe') && $request->input('tipe') !== 'all') {
            $query->where('tipe', $request->input('tipe'));
        }

        match ($request->input('sort')) {
            'oldest' => $query->oldest('id'),
            'nominal_desc' => $query->orderBy('nominal', 'desc'),
            'nominal_asc' => $query->orderBy('nominal', 'asc'),
            default => $query->latest('id'),
        };

        $perPage = min(max((int) $request->query('per_page', 6), 1), 50);
        $mutations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $mutations->items(),
            'meta' => [
                'current_page' => $mutations->currentPage(),
                'last_page' => $mutations->lastPage(),
                'per_page' => $mutations->perPage(),
                'total' => $mutations->total(),
                'from' => $mutations->firstItem(),
                'to' => $mutations->lastItem(),
            ],
        ]);
    }

    /**
     * List registered bank accounts for the owner.
     */
    public function bankAccounts(Request $request): JsonResponse
    {
        $accounts = $request->user()->rekeningBank()->latest('is_utama')->latest('id')->get();

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }

    /**
     * Add a new bank account.
     */
    public function addBankAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_bank' => 'required|string|max:50',
            'nomor_rekening' => 'required|string|max:50',
            'nama_pemilik' => 'required|string|max:100',
            'is_utama' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $isUtama = ! empty($validated['is_utama']);

        $account = DB::transaction(function () use ($user, $validated, $isUtama) {
            if ($isUtama || ! $user->rekeningBank()->exists()) {
                $user->rekeningBank()->update(['is_utama' => false]);
                $isUtama = true;
            }

            return $user->rekeningBank()->create([
                'nama_bank' => strtoupper(trim($validated['nama_bank'])),
                'nomor_rekening' => trim($validated['nomor_rekening']),
                'nama_pemilik' => trim($validated['nama_pemilik']),
                'is_utama' => $isUtama,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Rekening bank berhasil ditambahkan.',
            'data' => $account,
        ], 201);
    }

    /**
     * Delete a bank account.
     */
    public function deleteBankAccount(Request $request, int $id): JsonResponse
    {
        $account = $request->user()->rekeningBank()->findOrFail($id);

        if ($account->penarikanDana()->whereIn('status', ['menunggu', 'diproses'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak dapat dihapus karena sedang ada proses pencairan dana aktif.',
            ], 422);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rekening bank berhasil dihapus.',
        ]);
    }

    /**
     * Request a withdrawal (penarikan saldo).
     */
    public function requestWithdrawal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rekening_bank_id' => 'required|exists:rekening_bank_mitra,id',
            'nominal' => 'required|integer|min:50000',
        ]);

        $user = $request->user();
        $nominal = (int) $validated['nominal'];

        $rekening = $user->rekeningBank()->findOrFail($validated['rekening_bank_id']);

        try {
            $penarikan = DB::transaction(function () use ($user, $rekening, $nominal) {
                // Debit wallet immediately to hold the requested amount
                $penarikan = PenarikanDana::create([
                    'kode_penarikan' => 'WD-'.now()->format('Ymd').'-'.rand(1000, 9999),
                    'pengguna_id' => $user->id,
                    'rekening_bank_id' => $rekening->id,
                    'nominal' => $nominal,
                    'status' => 'menunggu',
                ]);

                $deskripsi = "Pengajuan penarikan dana ke {$rekening->nama_bank} ({$rekening->nomor_rekening})";
                $this->walletService->debit($user, $nominal, $deskripsi, $penarikan);

                return $penarikan;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan penarikan dana berhasil dikirim dan sedang menunggu verifikasi admin.',
                'data' => $penarikan->load('rekeningBank'),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * List owner's withdrawal requests history.
     */
    public function withdrawals(Request $request): JsonResponse
    {
        $query = $request->user()->penarikanDana()->with('rekeningBank');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        match ($request->input('sort')) {
            'oldest' => $query->oldest('id'),
            'nominal_desc' => $query->orderBy('nominal', 'desc'),
            'nominal_asc' => $query->orderBy('nominal', 'asc'),
            default => $query->latest('id'),
        };

        $perPage = min(max((int) $request->query('per_page', 6), 1), 50);
        $withdrawals = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $withdrawals->items(),
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
}
