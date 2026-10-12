<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kos\StoreKosRequest;
use App\Http\Requests\Kos\UpdateKosRequest;
use App\Http\Resources\KosResource;
use App\Models\Kos;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class KosController extends Controller
{
    /**
     * Create a new kos controller instance.
     */
    public function __construct(
        protected GeocodingService $geocodingService
    ) {}

    /**
     * Display a listing of kos with advanced search and filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Kos::query()->with(['pemilik', 'fasilitas', 'kamar']);

        // Filter: pencarian teks nama atau alamat (q)
        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('alamat', 'like', "%{$keyword}%");
            });
        }

        // Filter: lokasi
        if ($request->filled('lokasi')) {
            $lokasi = (string) $request->input('lokasi');
            $query->where('alamat', 'like', "%{$lokasi}%");
        }

        // Filter: harga minimum & maksimum
        if ($request->filled('harga_min')) {
            $query->where('harga', '>=', (int) $request->input('harga_min'));
        }
        if ($request->filled('harga_max')) {
            $query->where('harga', '<=', (int) $request->input('harga_max'));
        }

        // Filter: jenis (putra, putri, campur)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->input('jenis'));
        }

        // Filter: fasilitas (array of IDs)
        if ($request->filled('fasilitas')) {
            $fasilitasIds = (array) $request->input('fasilitas');
            foreach ($fasilitasIds as $fasilitasId) {
                $query->whereHas('fasilitas', function ($q) use ($fasilitasId) {
                    $q->where('fasilitas.id', $fasilitasId);
                });
            }
        }

        // Filter: hanya yang memiliki kamar tersedia
        if ($request->boolean('tersedia')) {
            $query->whereHas('kamar', function ($q) {
                $q->where('status', 'tersedia');
            });
        }

        // Filter: milik pemilik tertentu (opsional untuk filter dashboard pemilik)
        if ($request->filled('pemilik_id')) {
            $query->where('pemilik_id', $request->input('pemilik_id'));
        }

        // Pengurutan (Sorting)
        match ($request->input('sort')) {
            'harga_asc' => $query->orderBy('harga', 'asc'),
            'harga_desc' => $query->orderBy('harga', 'desc'),
            default => $query->latest(),
        };

        $perPage = min(max((int) $request->input('per_page', 12), 1), 100);
        $kosList = $query->paginate($perPage);

        return KosResource::collection($kosList);
    }

    /**
     * Store a newly created kos in storage.
     */
    public function store(StoreKosRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;

        // Jika koordinat tidak diinput, gunakan GeocodingService otomatis
        if ($latitude === null || $longitude === null) {
            $coords = $this->geocodingService->geocodeAddress($validated['alamat']);
            if ($coords) {
                $latitude = $coords['latitude'];
                $longitude = $coords['longitude'];
            }
        }

        $kos = Kos::create([
            'pemilik_id' => $user->id,
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'jenis' => $validated['jenis'],
            'harga' => $validated['harga'],
            'harga_harian' => $validated['harga_harian'] ?? null,
            'harga_mingguan' => $validated['harga_mingguan'] ?? null,
            'durasi_sewa' => $validated['durasi_sewa'] ?? ['bulanan'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'peraturan' => $validated['peraturan'] ?? null,
            'foto' => $validated['foto'] ?? null,
        ]);

        if (! empty($validated['fasilitas_ids'])) {
            $kos->fasilitas()->sync($validated['fasilitas_ids']);
        }

        $kos->load(['pemilik', 'fasilitas', 'kamar']);

        return response()->json([
            'message' => 'Data kos berhasil ditambahkan.',
            'data' => new KosResource($kos),
        ], 201);
    }

    /**
     * Display the specified kos with details.
     */
    public function show(int $id): JsonResponse
    {
        $kos = Kos::with(['pemilik', 'fasilitas', 'kamar', 'ulasan.pengguna'])->findOrFail($id);

        return response()->json([
            'data' => new KosResource($kos),
        ]);
    }

    /**
     * Update the specified kos in storage.
     */
    public function update(UpdateKosRequest $request, int $id): JsonResponse
    {
        $kos = Kos::findOrFail($id);
        Gate::authorize('update', $kos);

        $validated = $request->validated();

        $latitude = $validated['latitude'] ?? $kos->latitude;
        $longitude = $validated['longitude'] ?? $kos->longitude;

        // Jika alamat berubah dan koordinat baru tidak disediakan, geocode ulang
        if (isset($validated['alamat']) && $validated['alamat'] !== $kos->alamat && ! isset($validated['latitude'])) {
            $coords = $this->geocodingService->geocodeAddress($validated['alamat']);
            if ($coords) {
                $latitude = $coords['latitude'];
                $longitude = $coords['longitude'];
            }
        }

        $kos->update(array_merge($validated, [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]));

        if (isset($validated['fasilitas_ids'])) {
            $kos->fasilitas()->sync($validated['fasilitas_ids']);
        }

        $kos->load(['pemilik', 'fasilitas', 'kamar']);

        return response()->json([
            'message' => 'Data kos berhasil diperbarui.',
            'data' => new KosResource($kos),
        ]);
    }

    /**
     * Remove the specified kos from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $kos = Kos::findOrFail($id);
        Gate::authorize('delete', $kos);

        $kos->delete();

        return response()->json([
            'message' => 'Data kos berhasil dihapus.',
        ]);
    }
}
