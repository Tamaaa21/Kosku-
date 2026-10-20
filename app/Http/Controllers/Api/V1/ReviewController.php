<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews for the specified kos.
     */
    public function index(Request $request, int $kosId): AnonymousResourceCollection
    {
        $kos = Kos::findOrFail($kosId);
        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);

        $ulasanList = $kos->ulasan()
            ->with('pengguna')
            ->latest()
            ->paginate($perPage);

        return ReviewResource::collection($ulasanList);
    }

    /**
     * Check if the authenticated user is eligible to review the specified kos.
     */
    public function eligibility(Request $request, int $kosId): JsonResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'pencari') {
            return response()->json([
                'eligible' => false,
                'has_reviewed' => false,
                'existing_review' => null,
                'message' => 'Hanya akun pencari kos yang dapat memberikan ulasan.',
            ]);
        }

        $kos = Kos::findOrFail($kosId);

        $hasApprovedBooking = Pemesanan::where('kos_id', $kos->id)
            ->where('pengguna_id', $user->id)
            ->where('status', 'disetujui')
            ->exists();

        $existingReview = Ulasan::where('kos_id', $kos->id)
            ->where('pengguna_id', $user->id)
            ->first();

        return response()->json([
            'eligible' => $hasApprovedBooking,
            'has_reviewed' => $existingReview !== null,
            'existing_review' => $existingReview ? new ReviewResource($existingReview->load('pengguna')) : null,
            'message' => $hasApprovedBooking
                ? 'Pengguna memenuhi syarat untuk menulis ulasan.'
                : 'Ulasan hanya dapat diberikan setelah pengajuan sewa Anda disetujui oleh pemilik kos.',
        ]);
    }

    /**
     * Store or update a review for the specified kos.
     */
    public function store(Request $request, int $kosId): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'pencari') {
            return response()->json([
                'message' => 'Hanya akun pencari kos yang dapat memberikan ulasan.',
            ], 403);
        }

        $kos = Kos::findOrFail($kosId);

        $approvedBooking = Pemesanan::where('kos_id', $kos->id)
            ->where('pengguna_id', $user->id)
            ->where('status', 'disetujui')
            ->latest()
            ->first();

        if (! $approvedBooking) {
            return response()->json([
                'message' => 'Anda hanya dapat memberikan ulasan untuk kos yang pemesanan sewanya telah disetujui oleh pemilik.',
            ], 403);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $ulasan = Ulasan::updateOrCreate(
            [
                'pengguna_id' => $user->id,
                'kos_id' => $kos->id,
            ],
            [
                'pemesanan_id' => $approvedBooking->id,
                'rating' => $validated['rating'],
                'komentar' => $validated['komentar'],
            ]
        );

        $ulasan->load('pengguna');
        $kos->refresh();

        return response()->json([
            'message' => 'Ulasan Anda berhasil disimpan. Terima kasih atas kontribusi Anda!',
            'data' => new ReviewResource($ulasan),
            'rating_rata_rata' => $kos->rating_rata_rata,
            'ulasan_count' => $kos->ulasan_count,
        ], 200);
    }
}
