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
        ]);

        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if (isset($validated['nama'])) {
            $pengguna->nama = $validated['nama'];
        }

        if (array_key_exists('telepon', $validated) || array_key_exists('no_hp', $validated)) {
            $pengguna->telepon = $validated['telepon'] ?? $validated['no_hp'] ?? null;
        }

        $pengguna->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => new PenggunaResource($pengguna),
        ]);
    }
}
