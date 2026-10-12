<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\KosResource;
use App\Models\Kos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class AdminKosController extends Controller
{
    /**
     * Display a listing of all kos with admin moderation filtering, search, and room counters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Kos::query()
            ->with([
                'pemilik:id,nama,email,telepon',
                'fasilitas:id,nama',
                'kamar:id,kos_id,nama_kamar,harga,status',
            ])
            ->withCount('kamar')
            ->withCount([
                'kamar as kamar_tersedia_count' => fn ($q) => $q->where('status', 'tersedia'),
            ]);

        // Filter pencarian: nama kos, alamat, deskripsi, atau informasi pemilik kos
        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('alamat', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%")
                    ->orWhereHas('pemilik', function ($ownerQuery) use ($keyword) {
                        $ownerQuery->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%")
                            ->orWhere('telepon', 'like', "%{$keyword}%");
                    });
            });
        }

        // Filter jenis kos: putra, putri, campur
        if ($request->filled('jenis') && $request->input('jenis') !== 'all') {
            $query->where('jenis', $request->input('jenis'));
        }

        // Filter ketersediaan kamar
        if ($request->filled('status_kamar') && $request->input('status_kamar') !== 'all') {
            if ($request->input('status_kamar') === 'tersedia') {
                $query->whereHas('kamar', fn ($q) => $q->where('status', 'tersedia'));
            } elseif ($request->input('status_kamar') === 'penuh') {
                $query->whereDoesntHave('kamar', fn ($q) => $q->where('status', 'tersedia'));
            }
        }

        // Pengurutan (Sorting)
        match ($request->input('sort')) {
            'harga_asc' => $query->orderBy('harga', 'asc'),
            'harga_desc' => $query->orderBy('harga', 'desc'),
            'kamar_banyak' => $query->orderByDesc('kamar_count'),
            'nama_asc' => $query->orderBy('nama', 'asc'),
            default => $query->latest('id'),
        };

        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);
        $kosList = $query->paginate($perPage);

        return KosResource::collection($kosList);
    }

    /**
     * Remove the specified kos by admin moderation (safely cascaded).
     */
    public function destroy(int $id): JsonResponse
    {
        $kos = Kos::findOrFail($id);
        $kosNama = $kos->nama;

        DB::transaction(function () use ($kos) {
            $kos->pemesanan()->delete();
            $kos->kamar()->delete();
            $kos->fasilitas()->detach();
            $kos->delete();
        });

        return response()->json([
            'message' => "Properti kos '{$kosNama}' berhasil dihapus dari sistem (moderasi admin).",
        ]);
    }
}
