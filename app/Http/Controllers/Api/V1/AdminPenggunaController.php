<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePenggunaStatusRequest;
use App\Http\Resources\PenggunaResource;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminPenggunaController extends Controller
{
    /**
     * Display a listing of all users for admin management.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Pengguna::query()->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->has('aktif')) {
            $query->where('aktif', $request->boolean('aktif'));
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);
        $users = $query->paginate($perPage);

        return PenggunaResource::collection($users);
    }

    /**
     * Update active status of a user account.
     */
    public function updateStatus(UpdatePenggunaStatusRequest $request, int $id): JsonResponse
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->update([
            'aktif' => $request->validated('aktif'),
        ]);

        return response()->json([
            'message' => 'Status akun pengguna berhasil diperbarui.',
            'data' => new PenggunaResource($pengguna),
        ]);
    }
}
