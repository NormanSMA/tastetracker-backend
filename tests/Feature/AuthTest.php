<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Usuario puede hacer login con credenciales correctas
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        // Crear usuario de prueba
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'waiter',
            'is_active' => true,
        ]);

        // Intentar login
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Verificaciones
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'access_token',
                     'token_type',
                     'user'
                 ]);

        $this->assertEquals('Bearer', $response->json('token_type'));
        $this->assertNotNull($response->json('access_token'));
    }

    /**
     * Test: Login falla con contraseña incorrecta
     */
    public function test_login_fails_with_invalid_password(): void
    {
        // Crear usuario
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'waiter',
            'is_active' => true,
        ]);

        // Intentar login con contraseña incorrecta
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // Debe retornar 401 Unauthorized
        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Credenciales incorrectas (Email o contraseña inválidos)'
                 ]);
    }

    /**
     * Test: Usuario inactivo no puede hacer login
     */
    public function test_inactive_user_cannot_login(): void
    {
        // Crear usuario inactivo
        User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'role' => 'waiter',
            'is_active' => false, // Cuenta desactivada
        ]);

        // Intentar login
        $response = $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        // Debe retornar 403 Forbidden
        $response->assertStatus(403)
                 ->assertJson([
                     'message' => 'Tu cuenta está desactivada. Contacta al administrador.'
                 ]);
    }

    /**
     * Test: Usuario autenticado puede ver su perfil
     */
    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'waiter',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/user-profile');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id', 'name', 'email', 'role']
                 ]);
    }

    /**
     * Test: Usuario puede hacer logout
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Sesión cerrada exitosamente'
                 ]);
    }
}
