<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_as_pencari(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'nama' => 'Pencari Baru',
            'email' => 'pencari.baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'pencari',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'nama', 'email', 'role', 'aktif'],
                'token',
            ])
            ->assertJson([
                'user' => [
                    'nama' => 'Pencari Baru',
                    'email' => 'pencari.baru@example.com',
                    'role' => 'pencari',
                    'aktif' => true,
                ],
            ]);

        $this->assertDatabaseHas('pengguna', [
            'email' => 'pencari.baru@example.com',
            'role' => 'pencari',
        ]);
    }

    public function test_user_can_register_as_pemilik(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'nama' => 'Pemilik Baru',
            'email' => 'pemilik.baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'pemilik',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'role' => 'pemilik',
                ],
            ]);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        Pengguna::factory()->create(['email' => 'duplikat@example.com']);

        $response = $this->postJson('/api/v1/register', [
            'nama' => 'User Duplikat',
            'email' => 'duplikat@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = Pengguna::factory()->create([
            'email' => 'login.test@example.com',
            'password' => Hash::make('secret123'),
            'aktif' => true,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'login.test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user',
                'token',
            ])
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'email' => 'login.test@example.com',
                ],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        Pengguna::factory()->create([
            'email' => 'valid@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'valid@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_inactive_user_cannot_login(): void
    {
        Pengguna::factory()->nonaktif()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'inactive@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Akun Anda dinonaktifkan oleh administrator.',
            ]);
    }

    public function test_authenticated_user_can_view_own_profile(): void
    {
        $user = Pengguna::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_authenticated_user_can_update_own_profile(): void
    {
        $user = Pengguna::factory()->create(['nama' => 'Nama Lama', 'telepon' => '08111111111']);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/me', [
            'nama' => 'Nama Baru',
            'no_hp' => '08999999999',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profil berhasil diperbarui.',
                'user' => [
                    'id' => $user->id,
                    'nama' => 'Nama Baru',
                    'telepon' => '08999999999',
                    'no_hp' => '08999999999',
                ],
            ]);

        $this->assertDatabaseHas('pengguna', [
            'id' => $user->id,
            'nama' => 'Nama Baru',
            'telepon' => '08999999999',
        ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = Pengguna::factory()->create();
        $token = $user->createToken('test_token');

        $response = $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout berhasil.',
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_authenticated_user_can_update_password(): void
    {
        $user = Pengguna::factory()->create([
            'password' => Hash::make('password_lama123'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/me/password', [
            'current_password' => 'password_lama123',
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Kata sandi berhasil diperbarui.',
            ]);

        $this->assertTrue(Hash::check('password_baru123', $user->fresh()->password));
    }

    public function test_update_password_fails_if_current_password_is_incorrect(): void
    {
        $user = Pengguna::factory()->create([
            'password' => Hash::make('password_lama123'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/me/password', [
            'current_password' => 'salah_password',
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_authenticated_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = Pengguna::factory()->create();
        Sanctum::actingAs($user);

        $file = $this->createFakeImage('avatar.jpg', 500);

        $response = $this->postJson('/api/v1/me/avatar', [
            'foto' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'nama', 'email', 'foto'],
            ]);

        $this->assertNotNull($user->fresh()->foto);
        $this->assertStringContainsString('avatars/', $user->fresh()->foto);
    }
}
