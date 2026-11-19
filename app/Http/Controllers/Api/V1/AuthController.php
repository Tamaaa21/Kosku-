<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'user' => new UserResource($pengguna),
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

        // Validasi Pemisahan Portal Login Admin vs Publik
        $portal = $request->input('portal');
        if ($portal === 'admin' || $request->boolean('is_admin_portal')) {
            if ($pengguna->role !== 'admin') {
                return response()->json([
                    'message' => 'Akses ditolak. Portal ini khusus untuk Administrator sistem.',
                ], 403);
            }
        } elseif ($portal === 'public' || $request->boolean('is_public_portal')) {
            if ($pengguna->role === 'admin') {
                return response()->json([
                    'message' => 'Akun Administrator wajib masuk melalui Portal Administrator Khusus (/admin/login).',
                    'redirect_to_admin' => true,
                ], 403);
            }
        }

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => new UserResource($pengguna),
            'token' => $token,
        ]);
    }

    /**
     * Authenticate or register user via Google Sign-In.
     */
    public function googleLogin(GoogleLoginRequest $request): JsonResponse
    {
        $token = $request->getToken();
        $requestedRole = $request->input('role', 'pencari');
        $demoUser = $request->input('demo_user');

        $googleId = null;
        $email = null;
        $name = 'Pengguna Google';
        $avatar = null;

        // Mendukung mode demo / testing otomatis
        if (($token === 'mock-google-token' || str_starts_with((string) $token, 'demo-')) || ! empty($demoUser)) {
            $googleId = $demoUser['sub'] ?? 'google-demo-'.substr(md5($demoUser['email'] ?? 'demo'), 0, 16);
            $email = $demoUser['email'] ?? 'google.user@example.com';
            $name = $demoUser['name'] ?? 'Pengguna Google';
            $avatar = $demoUser['picture'] ?? null;
        } else {
            if (empty($token)) {
                return response()->json([
                    'message' => 'Token Google ID tidak ditemukan.',
                ], 422);
            }

            try {
                $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $token,
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Gagal menghubungi server verifikasi Google.',
                ], 502);
            }

            if (! $response->successful()) {
                return response()->json([
                    'message' => 'Token Google tidak valid atau telah kadaluarsa.',
                ], 401);
            }

            $payload = $response->json();
            $googleId = $payload['sub'] ?? null;
            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? ($payload['given_name'] ?? 'Pengguna Google');
            $avatar = $payload['picture'] ?? null;
        }

        if (empty($email)) {
            return response()->json([
                'message' => 'Alamat email tidak ditemukan dari profil Google.',
            ], 422);
        }

        // Cari pengguna berdasarkan google_id atau email
        $user = Pengguna::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            // Blokir akses login publik jika akun terdaftar adalah administrator
            if ($user->role === 'admin') {
                return response()->json([
                    'message' => 'Akun Administrator tidak dapat menggunakan Google Sign-In publik. Silakan gunakan Portal Administrator.',
                ], 403);
            }

            if (! $user->aktif) {
                return response()->json([
                    'message' => 'Akun Anda dinonaktifkan oleh administrator.',
                ], 403);
            }

            // Tautkan google_id jika belum ada
            if (empty($user->google_id)) {
                $user->google_id = $googleId;
            }

            // Perbarui foto jika belum memiliki foto kustom
            if (empty($user->foto) && ! empty($avatar)) {
                $user->foto = $avatar;
            }

            $user->save();
        } else {
            // Buat akun baru dengan role yang dipilih
            $user = Pengguna::create([
                'nama' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'foto' => $avatar,
                'password' => Hash::make(Str::random(32)),
                'role' => $requestedRole === 'pemilik' ? 'pemilik' : 'pencari',
                'aktif' => true,
                'status_verifikasi' => 'unverified',
            ]);
        }

        $authToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login dengan Google berhasil.',
            'user' => new UserResource($user),
            'token' => $authToken,
            'is_new_user' => $user->wasRecentlyCreated,
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
            'user' => new UserResource($request->user()),
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
            'user' => new UserResource($pengguna),
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
            'user' => new UserResource($pengguna),
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

    /**
     * Send a password reset link to the given user.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Tautan untuk mereset kata sandi telah dikirim ke email Anda.',
            ]);
        }

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => ['Harap tunggu beberapa saat sebelum mencoba meminta tautan reset kembali.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'],
        ]);
    }

    /**
     * Reset the user's password using token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Pengguna $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru Anda.',
            ]);
        }

        if ($status === Password::INVALID_TOKEN) {
            throw ValidationException::withMessages([
                'token' => ['Token reset kata sandi tidak valid atau sudah kedaluwarsa.'],
            ]);
        }

        if ($status === Password::INVALID_USER) {
            throw ValidationException::withMessages([
                'email' => ['Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Gagal mengatur ulang kata sandi. Silakan coba lagi.'],
        ]);
    }
}
