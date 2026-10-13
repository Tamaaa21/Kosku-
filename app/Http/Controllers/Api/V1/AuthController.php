<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\PenggunaResource;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user account.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $pengguna = Pengguna::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'telepon' => $validated['telepon'] ?? $validated['no_hp'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'pencari',
            'aktif' => true,
        ]);

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user' => new PenggunaResource($pengguna),
            'token' => $token,
        ], 201);
    }

    /**
     * Authenticate user and issue an API token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $pengguna = Pengguna::where('email', $validated['email'])->first();

        if (! $pengguna || ! Hash::check($validated['password'], $pengguna->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        if (! $pengguna->aktif) {
            return response()->json([
                'message' => 'Akun Anda dinonaktifkan oleh administrator.',
            ], 403);
        }

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => new PenggunaResource($pengguna),
            'token' => $token,
        ]);
    }

    /**
     * Revoke current user's token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Retrieve authenticated user details.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new PenggunaResource($request->user()),
        ]);
    }

    /**
     * Update current authenticated user's profile information.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['sometimes', 'required', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'foto' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if (isset($validated['nama'])) {
            $pengguna->nama = $validated['nama'];
        }

        if (array_key_exists('telepon', $validated) || array_key_exists('no_hp', $validated)) {
            $pengguna->telepon = $validated['telepon'] ?? $validated['no_hp'] ?? null;
        }

        if (array_key_exists('foto', $validated)) {
            $pengguna->foto = $validated['foto'];
        }

        $pengguna->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => new PenggunaResource($pengguna),
        ]);
    }

    /**
     * Update current authenticated user's avatar image.
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'foto' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'foto.required' => 'Berkas foto profil harus diunggah.',
            'foto.image' => 'Berkas harus berupa gambar valid.',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto profil maksimal 2MB (2048 KB).',
        ]);

        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if ($pengguna->foto) {
            $oldPath = str_replace(Storage::disk('public')->url(''), '', $pengguna->foto);
            $oldPath = ltrim($oldPath, '/');
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $path = $request->file('foto')->store('avatars', 'public');
        $pengguna->foto = Storage::disk('public')->url($path);
        $pengguna->save();

        return response()->json([
            'message' => 'Foto profil berhasil diperbarui.',
            'user' => new PenggunaResource($pengguna),
        ]);
    }

    /**
     * Update current authenticated user's password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if (! Hash::check($validated['current_password'], $pengguna->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Kata sandi saat ini yang Anda masukkan salah.'],
            ]);
        }

        $pengguna->password = Hash::make($validated['password']);
        $pengguna->save();

        return response()->json([
            'message' => 'Kata sandi berhasil diperbarui.',
        ]);
    }
}
