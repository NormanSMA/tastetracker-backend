<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * Obtener datos del usuario autenticado
     */
    public function show()
    {
        return response()->json([
            'data' => new UserResource(auth()->user())
        ]);
    }

    /**
     * POST /api/profile/update
     * Actualizar perfil del usuario autenticado
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        // Manejar foto de perfil
        if ($request->hasFile('photo')) {
            // Eliminar foto antigua si existe
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        $user->update($data);
        $user->refresh();

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * PUT /api/profile/password
     * Cambiar contraseña del usuario autenticado
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta']
            ]);
        }

        // Actualizar con la nueva contraseña
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }
}
