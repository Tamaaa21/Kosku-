<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kamar\StoreKamarRequest;
use App\Http\Requests\Kamar\UpdateKamarRequest;
use App\Http\Resources\KamarResource;
use App\Models\Kamar;
use App\Models\Kos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class KamarController extends Controller
{
    /**
     * Display a listing of rooms for a specific kos.
     */
    public function index(int $kosId): AnonymousResourceCollection
    {
        $kos = Kos::findOrFail($kosId);
        $kamar = $kos->kamar()->orderBy('id', 'asc')->get();

        return KamarResource::collection($kamar);
    }

    /**
     * Store a newly created room in a specific kos.
     */
    public function store(StoreKamarRequest $request, int $kosId): JsonResponse
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
            'data' => new KamarResource($kamar),
        ], 201);
    }

    /**
     * Update the specified room in storage.
     */
    public function update(UpdateKamarRequest $request, int $id): JsonResponse
    {
        $kamar = Kamar::with('kos')->findOrFail($id);
        Gate::authorize('update', $kamar);

        $kamar->update($request->validated());
        $kamar->kos?->syncPricesAndDurationsFromRooms();

        return response()->json([
            'message' => 'Data kamar berhasil diperbarui.',
            'data' => new KamarResource($kamar),
        ]);
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $kamar = Kamar::with('kos')->findOrFail($id);
        Gate::authorize('delete', $kamar);

        $kos = $kamar->kos;
        $kamar->delete();
        $kos?->syncPricesAndDurationsFromRooms();

        return response()->json([
            'message' => 'Kamar berhasil dihapus.',
        ]);
    }
}
