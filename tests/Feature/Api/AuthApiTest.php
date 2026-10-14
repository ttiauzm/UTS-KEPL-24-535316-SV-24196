<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_berhasil_dan_mengembalikan_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Tia',
            'email' => 'tia@example.com',
            'password' => 'rahasia123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email'], 'token']);
        $this->assertDatabaseHas('users', ['email' => 'tia@example.com']);
    }

    public function test_login_dengan_kredensial_benar_mengembalikan_token(): void
    {
        User::factory()->create([
            'email' => 'tia@example.com',
            'password' => 'rahasia123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'tia@example.com',
            'password' => 'rahasia123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'token', 'token_type', 'user']);
    }

    public function test_login_dengan_password_salah_ditolak(): void
    {
        User::factory()->create(['email' => 'tia@example.com', 'password' => 'rahasia123']);

        $this->postJson('/api/login', [
            'email' => 'tia@example.com',
            'password' => 'salah-total',
        ])->assertStatus(401)->assertJson(['message' => 'Email atau password salah.']);
    }

    public function test_login_validasi_gagal_jika_data_kosong(): void
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_endpoint_me_butuh_token(): void
    {
        $this->getJson('/api/me')->assertStatus(401);

        $token = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/me')->assertOk();
    }

    public function test_logout_menghapus_token(): void
    {
        $token = User::factory()->create()->createToken('test')->plainTextToken;
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
