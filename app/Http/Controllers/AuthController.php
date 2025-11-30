<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // POST /api/login
    public function login(Request $request)
    {
        // 1. Validar datos de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buscar usuario
        $user = User::where('email', $request->email)->first();

        // 3. Verificar contraseña
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas (Email o contraseña inválidos)'
            ], 401);
        }

        // 4. Verificar si está activo (Regla de negocio extra)
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Tu cuenta está desactivada. Contacta al administrador.'
            ], 403);
        }

        // 5. Generar Token (Sanctum)
        // Borramos tokens anteriores para evitar acumulación (opcional, buena práctica de seguridad)
        $user->tokens()->delete();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Retornar respuesta
        return response()->json([
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        // Elimina el token que se usó para esta petición
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

    // GET /api/user-profile
    public function userProfile(Request $request)
    {
        return response()->json([
            'message' => 'Datos del usuario autenticado',
            'user' => $request->user()
        ]);
    }
}
