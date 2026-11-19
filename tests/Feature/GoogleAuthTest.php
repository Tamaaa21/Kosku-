<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_can_register_as_pencari_via_google(): void
    {
        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'mock-google-token',
            'role' => 'pencari',
            'demo_user' => [
                'sub' => 'google-sub-12345',
                'email' => 'google.pencari@example.com',
                'name' => 'Budi Google',
                'picture' => 'https://example.com/avatar.jpg',
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Login dengan Google berhasil.')
            ->assertJsonPath('user.email', 'google.pencari@example.com')
            ->assertJsonPath('user.nama', 'Budi Google')
            ->assertJsonPath('user.role', 'pencari')
            ->assertJsonPath('user.has_google', true)
            ->assertJsonPath('is_new_user', true);

        $this->assertDatabaseHas('pengguna', [
            'email' => 'google.pencari@example.com',
            'google_id' => 'google-sub-12345',
            'role' => 'pencari',
            'aktif' => true,
        ]);
    }

    public function test_new_user_can_register_as_pemilik_via_google_with_unverified_status(): void
    {
        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'mock-google-token',
            'role' => 'pemilik',
            'demo_user' => [
                'sub' => 'google-sub-67890',
                'email' => 'juragan.google@example.com',
                'name' => 'Haji Google',
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.email', 'juragan.google@example.com')
            ->assertJsonPath('user.role', 'pemilik')
            ->assertJsonPath('user.status_verifikasi', 'unverified')
            ->assertJsonPath('is_new_user', true);

        $this->assertDatabaseHas('pengguna', [
            'email' => 'juragan.google@example.com',
            'google_id' => 'google-sub-67890',
            'role' => 'pemilik',
            'status_verifikasi' => 'unverified',
        ]);
    }

    public function test_existing_user_can_login_and_link_google_id(): void
    {
        $user = Pengguna::factory()->create([
            'email' => 'existing.user@example.com',
            'nama' => 'Existing User',
            'role' => 'pencari',
            'google_id' => null,
            'aktif' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'mock-google-token',
            'demo_user' => [
                'sub' => 'google-sub-linked',
                'email' => 'existing.user@example.com',
                'name' => 'Existing User Google',
                'picture' => 'https://example.com/avatar.jpg',
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.google_id', 'google-sub-linked')
            ->assertJsonPath('is_new_user', false);

        $this->assertDatabaseHas('pengguna', [
            'id' => $user->id,
            'google_id' => 'google-sub-linked',
        ]);
    }

    public function test_admin_account_cannot_login_via_public_google_auth(): void
    {
        Pengguna::factory()->create([
            'email' => 'admin@kos.test',
            'role' => 'admin',
            'google_id' => 'google-admin-123',
            'aktif' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'mock-google-token',
            'demo_user' => [
                'sub' => 'google-admin-123',
                'email' => 'admin@kos.test',
            ],
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Akun Administrator tidak dapat menggunakan Google Sign-In publik. Silakan gunakan Portal Administrator.');
    }

    public function test_inactive_user_cannot_login_via_google(): void
    {
        Pengguna::factory()->create([
            'email' => 'blocked@example.com',
            'role' => 'pencari',
            'google_id' => 'google-blocked-123',
            'aktif' => false,
        ]);

        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'mock-google-token',
            'demo_user' => [
                'sub' => 'google-blocked-123',
                'email' => 'blocked@example.com',
            ],
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Akun Anda dinonaktifkan oleh administrator.');
    }

    public function test_google_login_with_real_api_mock(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'sub' => 'real-google-id-999',
                'email' => 'real.google@example.com',
                'name' => 'Real Google User',
                'picture' => 'https://lh3.googleusercontent.com/photo.jpg',
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'valid-real-google-jwt-token',
            'role' => 'pencari',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.email', 'real.google@example.com')
            ->assertJsonPath('user.google_id', 'real-google-id-999');
    }

    public function test_portal_separation_login_validation(): void
    {
        $admin = Pengguna::factory()->create([
            'email' => 'superadmin@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        $pencari = Pengguna::factory()->create([
            'email' => 'userpencari@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'pencari',
            'aktif' => true,
        ]);

        // 1. Admin login di portal publik -> ditolak
        $publicAdminAttempt = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'secret123',
            'portal' => 'public',
        ]);
        $publicAdminAttempt->assertStatus(403)
            ->assertJsonPath('redirect_to_admin', true);

        // 2. Admin login di portal admin -> sukses
        $adminPortalSuccess = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@example.com',
            'password' => 'secret123',
            'portal' => 'admin',
        ]);
        $adminPortalSuccess->assertStatus(200)
            ->assertJsonPath('user.role', 'admin');

        // 3. Pencari login di portal admin -> ditolak
        $pencariAdminAttempt = $this->postJson('/api/v1/auth/login', [
            'email' => 'userpencari@example.com',
            'password' => 'secret123',
            'portal' => 'admin',
        ]);
        $pencariAdminAttempt->assertStatus(403)
            ->assertJsonPath('message', 'Akses ditolak. Portal ini khusus untuk Administrator sistem.');

        // 4. Pencari login di portal publik -> sukses
        $pencariPublicSuccess = $this->postJson('/api/v1/auth/login', [
            'email' => 'userpencari@example.com',
            'password' => 'secret123',
            'portal' => 'public',
        ]);
        $pencariPublicSuccess->assertStatus(200)
            ->assertJsonPath('user.role', 'pencari');
    }
}
