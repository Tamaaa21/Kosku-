<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Upload up to 5 images (max 2MB per file) for kos and rooms.
     */
    public function uploadImages(Request $request): JsonResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'images.required' => 'Setidaknya satu berkas gambar harus dipilih.',
            'images.max' => 'Maksimal 5 gambar dapat diunggah sekaligus.',
            'images.*.image' => 'Berkas harus berupa gambar valid.',
            'images.*.mimes' => 'Format gambar harus berupa JPG, PNG, atau WEBP.',
            'images.*.max' => 'Ukuran masing-masing gambar maksimal 2MB (2048 KB).',
        ]);

        $uploadedUrls = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('uploads', 'public');
            $uploadedUrls[] = Storage::disk('public')->url($path);
        }

        return response()->json([
            'message' => 'Gambar berhasil diunggah.',
            'urls' => $uploadedUrls,
        ], 201);
    }
}
