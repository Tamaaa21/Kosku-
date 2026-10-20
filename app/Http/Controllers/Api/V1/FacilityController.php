<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\StoreFacilityRequest;
use App\Http\Requests\Facility\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Fasilitas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FacilityController extends Controller
{
    /**
     * Display a listing of amenities.
     */
    public function index(): AnonymousResourceCollection
    {
        $fasilitas = Fasilitas::withCount('kos')->orderBy('nama')->get();

        return FacilityResource::collection($fasilitas);
    }

    /**
     * Store a newly created amenity (Admin only).
     */
    public function store(StoreFacilityRequest $request): JsonResponse
    {
        $fasilitas = Fasilitas::create($request->validated());

        return response()->json([
            'message' => 'Fasilitas berhasil ditambahkan.',
            'data' => new FacilityResource($fasilitas),
        ], 201);
    }

    /**
     * Update the specified amenity (Admin only).
     */
    public function update(UpdateFacilityRequest $request, int $id): JsonResponse
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->update($request->validated());

        return response()->json([
            'message' => 'Fasilitas berhasil diperbarui.',
            'data' => new FacilityResource($fasilitas),
        ]);
    }

    /**
     * Remove the specified amenity (Admin only).
     */
    public function destroy(int $id): JsonResponse
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->delete();

        return response()->json([
            'message' => 'Fasilitas berhasil dihapus.',
        ]);
    }
}
