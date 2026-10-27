<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyVerificationController extends Controller
{
    /**
     * Get verification status and submitted documents for the authenticated owner.
     */
    public function show(Request $request): JsonResponse
    {
        /** @var Pengguna $user */
        $user = $request->user();

        return response()->json([
            'data' => [
                'status_verifikasi' => $user->status_verifikasi ?? 'unverified',
                'is_verified' => $user->status_verifikasi === 'verified',
                'nik' => $user->nik,
                'foto_ktp' => $user->foto_ktp,
                'bukti_properti' => $user->bukti_properti,
                'foto_properti' => $user->foto_properti,
                'catatan_verifikasi' => $user->catatan_verifikasi,
                'alasan_penolakan' => $user->alasan_penolakan,
                'verified_at' => $user->verified_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Submit ownership verification documents.
     */
    public function submit(Request $request): JsonResponse
    {
        /** @var Pengguna $user */
        $user = $request->user();

        $validated = $request->validate([
            'nik' => ['required', 'string', 'digits_between:16,20'],
            'foto_ktp' => ['required', 'string', 'max:500'],
            'bukti_properti' => ['required', 'string', 'max:500'],
            'foto_properti' => ['required', 'string', 'max:500'],
            'catatan_verifikasi' => ['nullable', 'string', 'max:500'],
        ], [
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.digits_between' => 'NIK harus berjumlah 16 digit angka.',
            'foto_ktp.required' => 'Foto e-KTP wajib diunggah.',
            'bukti_properti.required' => 'Bukti kepemilikan / tagihan properti (PBB/Listrik) wajib diunggah.',
            'foto_properti.required' => 'Foto tampak depan / di lokasi kos wajib diunggah.',
            'catatan_verifikasi.max' => 'Catatan verifikasi maksimal 500 karakter.',
        ]);

        $user->update([
            'nik' => $validated['nik'],
            'foto_ktp' => $validated['foto_ktp'],
            'bukti_properti' => $validated['bukti_properti'],
            'foto_properti' => $validated['foto_properti'],
            'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? null,
            'status_verifikasi' => 'pending',
            'alasan_penolakan' => null,
        ]);

        return response()->json([
            'message' => 'Pengajuan verifikasi kepemilikan properti berhasil dikirim. Tim Kostara akan meninjau dokumen Anda dalam 1x24 jam.',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Upload single document file for verification (KTP, PBB, or building photo).
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ], [
            'file.required' => 'Berkas dokumen wajib diunggah.',
            'file.image' => 'Berkas harus berupa gambar valid.',
            'file.mimes' => 'Format berkas harus JPG, PNG, atau WEBP.',
            'file.max' => 'Ukuran berkas maksimal 3MB (3072 KB).',
        ]);

        $path = $request->file('file')->store('verifikasi', 'public');
        $url = Storage::disk('public')->url($path);

        return response()->json([
            'message' => 'Dokumen berhasil diunggah.',
            'url' => $url,
        ], 201);
    }

    /**
     * Admin: List verification requests from owners.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Pengguna::query()
            ->where('role', 'pemilik');

        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->input('status'));
        }

        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%");
            });
        }

        $query->orderByRaw("CASE 
                WHEN status_verifikasi = 'pending' THEN 1 
                WHEN status_verifikasi = 'unverified' THEN 2 
                WHEN status_verifikasi = 'rejected' THEN 3 
                ELSE 4 
            END")
            ->latest('updated_at');

        $users = $query->paginate(20);

        return response()->json([
            'data' => UserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Admin: Review and update verification status (approve or reject).
     */
    public function adminReview(Request $request, int $id): JsonResponse
    {
        $user = Pengguna::where('role', 'pemilik')->findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'alasan_penolakan' => ['required_if:status,rejected', 'nullable', 'string', 'max:500'],
        ], [
            'status.required' => 'Keputusan verifikasi wajib ditentukan.',
            'status.in' => 'Status hanya boleh bernilai verified atau rejected.',
            'alasan_penolakan.required_if' => 'Alasan penolakan wajib diisi jika menolak pengajuan verifikasi.',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $status = $validated['status'];
        $isVerified = $status === 'verified';

        $user->update([
            'status_verifikasi' => $status,
            'alasan_penolakan' => $isVerified ? null : ($validated['alasan_penolakan'] ?? null),
            'verified_at' => $isVerified ? now() : null,
        ]);

        $message = $isVerified
            ? "Verifikasi kepemilikan untuk {$user->nama} berhasil disetujui. Fitur kelola kos telah aktif."
            : "Verifikasi kepemilikan untuk {$user->nama} telah ditolak dengan catatan perbaikan.";

        return response()->json([
            'message' => $message,
            'data' => new UserResource($user),
        ]);
    }
}
