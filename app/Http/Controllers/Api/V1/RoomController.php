<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms for a specific kos.
     */
    public function index(Request $request, int $kosId): AnonymousResourceCollection
    {
        $kos = Kos::findOrFail($kosId);
        $query = $kos->kamar();

        // Filter: pencarian nama / nomor kamar
        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $query->where('nama_kamar', 'like', "%{$keyword}%");
        }

        // Filter: status kamar
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Sorting
        match ($request->input('sort')) {
            'nama_asc' => $query->orderBy('nama_kamar', 'asc'),
            'nama_desc' => $query->orderBy('nama_kamar', 'desc'),
            'harga_asc' => $query->orderBy('harga', 'asc'),
            'harga_desc' => $query->orderBy('harga', 'desc'),
            'status_tersedia' => $query->orderByRaw("CASE WHEN status = 'tersedia' THEN 0 ELSE 1 END")->orderBy('nama_kamar', 'asc'),
            'status_terisi' => $query->orderByRaw("CASE WHEN status = 'terisi' THEN 0 ELSE 1 END")->orderBy('nama_kamar', 'asc'),
            'latest' => $query->latest(),
            'oldest' => $query->oldest(),
            default => $query->orderByRaw("CASE WHEN status = 'tersedia' THEN 0 ELSE 1 END")->orderBy('id', 'asc'),
        };

        $countsQuery = $kos->kamar();
        $totalAll = (clone $countsQuery)->count();
        $totalTersedia = (clone $countsQuery)->where('status', 'tersedia')->count();
        $totalTerisi = (clone $countsQuery)->where('status', 'terisi')->count();
        $totalEstimasiPendapatan = (int) (clone $countsQuery)->sum('harga');
        $totalPendapatanAktif = (int) (clone $countsQuery)->where('status', 'terisi')->sum('harga');

        $additionalMeta = [
            'counts' => [
                'total' => $totalAll,
                'tersedia' => $totalTersedia,
                'terisi' => $totalTerisi,
                'estimasi_pendapatan' => $totalEstimasiPendapatan,
                'pendapatan_aktif' => $totalPendapatanAktif,
            ],
        ];

        if ($request->input('per_page') === 'all') {
            return RoomResource::collection($query->get())->additional($additionalMeta);
        }

        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);
        $kamar = $query->paginate($perPage);

        return RoomResource::collection($kamar)->additional($additionalMeta);
    }

    /**
     * Store a newly created room in a specific kos.
     */
    public function store(StoreRoomRequest $request, int $kosId): JsonResponse
    {
        $kos = Kos::findOrFail($kosId);
        Gate::authorize('create', [Kamar::class, $kos]);

        $validated = $request->validated();

        $kamar = $kos->kamar()->create([
            'nama_kamar' => $validated['nama_kamar'],
            'harga' => $validated['harga'],
            'harga_harian' => $validated['harga_harian'] ?? null,
            'harga_mingguan' => $validated['harga_mingguan'] ?? null,
            'durasi_sewa' => $validated['durasi_sewa'] ?? ['bulanan'],
            'status' => $validated['status'] ?? 'tersedia',
            'ukuran' => $validated['ukuran'] ?? null,
            'foto' => $validated['foto'] ?? null,
            'fasilitas_kamar' => $validated['fasilitas_kamar'] ?? null,
        ]);

        $kos->syncPricesAndDurationsFromRooms();

        return response()->json([
            'message' => 'Kamar berhasil ditambahkan.',
            'data' => new RoomResource($kamar),
        ], 201);
    }

    /**
     * Update the specified room in storage.
     */
    public function update(UpdateRoomRequest $request, int $id): JsonResponse
    {
        $kamar = Kamar::with('kos')->findOrFail($id);
        Gate::authorize('update', $kamar);

        $validated = $request->validated();

        // Cegah perubahan status menjadi 'tersedia' jika kamar sedang memiliki sewa aktif (disetujui)
        if (isset($validated['status']) && $validated['status'] === 'tersedia') {
            $hasActiveBooking = $kamar->pemesanan()
                ->where('status', 'disetujui')
                ->exists();

            if ($hasActiveBooking) {
                return response()->json([
                    'message' => 'Status kamar tidak dapat diubah menjadi tersedia karena sedang memiliki sewa aktif (disetujui).',
                ], 422);
            }
        }

        $kamar->update($validated);
        $kamar->kos?->syncPricesAndDurationsFromRooms();

        return response()->json([
            'message' => 'Data kamar berhasil diperbarui.',
            'data' => new RoomResource($kamar),
        ]);
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $kamar = Kamar::with('kos')->findOrFail($id);
        Gate::authorize('delete', $kamar);

        $hasActiveBooking = $kamar->pemesanan()
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($hasActiveBooking) {
            return response()->json([
                'message' => 'Kamar tidak dapat dihapus karena sedang memiliki transaksi pemesanan aktif.',
            ], 422);
        }

        if ($kamar->pemesanan()->exists()) {
            return response()->json([
                'message' => 'Kamar tidak dapat dihapus karena memiliki riwayat data pemesanan terkait.',
            ], 422);
        }

        $kos = $kamar->kos;
        $kamar->delete();
        $kos?->syncPricesAndDurationsFromRooms();

        return response()->json([
            'message' => 'Kamar berhasil dihapus.',
        ]);
    }
}
