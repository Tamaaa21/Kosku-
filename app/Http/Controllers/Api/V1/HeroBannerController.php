<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeroBannerResource;
use App\Models\HeroBanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HeroBannerController extends Controller
{
    /**
     * Display active hero banners for public homepage.
     */
    public function publicIndex(): AnonymousResourceCollection
    {
        $banners = HeroBanner::active()->ordered()->get();

        return HeroBannerResource::collection($banners);
    }

    /**
     * Display all hero banners for administration.
     */
    public function index(): AnonymousResourceCollection
    {
        $banners = HeroBanner::ordered()->get();

        return HeroBannerResource::collection($banners);
    }

    /**
     * Store a newly created hero banner (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
            'subjudul' => ['nullable', 'string', 'max:1000'],
            'badge_text' => ['nullable', 'string', 'max:255'],
            'gambar_url' => ['required', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'gambar_url.required' => 'URL atau berkas gambar slider wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka bulat.',
        ]);

        $banner = HeroBanner::create([
            'judul' => $validated['judul'] ?? null,
            'subjudul' => $validated['subjudul'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'gambar_url' => $validated['gambar_url'],
            'urutan' => $validated['urutan'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Banner slider berhasil ditambahkan.',
            'data' => new HeroBannerResource($banner),
        ], 201);
    }

    /**
     * Display the specified hero banner.
     */
    public function show(int $id): JsonResponse
    {
        $banner = HeroBanner::findOrFail($id);

        return response()->json([
            'data' => new HeroBannerResource($banner),
        ]);
    }

    /**
     * Update the specified hero banner (Admin only).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $banner = HeroBanner::findOrFail($id);

        $validated = $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
            'subjudul' => ['nullable', 'string', 'max:1000'],
            'badge_text' => ['nullable', 'string', 'max:255'],
            'gambar_url' => ['required', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'gambar_url.required' => 'URL atau berkas gambar slider wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka bulat.',
        ]);

        $banner->update($validated);

        return response()->json([
            'message' => 'Banner slider berhasil diperbarui.',
            'data' => new HeroBannerResource($banner),
        ]);
    }

    /**
     * Update active status for the specified hero banner (Admin only).
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $banner = HeroBanner::findOrFail($id);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $banner->update([
            'is_active' => $validated['is_active'],
        ]);

        return response()->json([
            'message' => 'Status banner slider berhasil diubah.',
            'data' => new HeroBannerResource($banner),
        ]);
    }

    /**
     * Remove the specified hero banner (Admin only).
     */
    public function destroy(int $id): JsonResponse
    {
        $banner = HeroBanner::findOrFail($id);
        $banner->delete();

        return response()->json([
            'message' => 'Banner slider berhasil dihapus.',
        ]);
    }
}
