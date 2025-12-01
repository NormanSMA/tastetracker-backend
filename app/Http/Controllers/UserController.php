<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * GET /api/users
     * Lista todos los usuarios
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => UserResource::collection($users)]);
    }

    /**
     * POST /api/users
     * Crear nuevo usuario
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Encriptar contraseña
        $data['password'] = Hash::make($data['password']);

        // Manejar foto de perfil
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        $user = User::create($data);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * GET /api/users/{id}
     * Ver detalle de usuario
     */
    public function show(User $user)
    {
        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * PUT /api/users/{id}
     * Actualizar usuario
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $data = $request->validated();

        // Solo encriptar si se envió nueva contraseña
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // No actualizar si viene vacío
        }

        // Manejar foto de perfil
        if ($request->hasFile('photo')) {
            // Eliminar foto antigua si existe
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        $user->update($data);

        // Refrescar el modelo para obtener los datos actualizados
        $user->refresh();

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * DELETE /api/users/{id}
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        // Eliminar foto de perfil si existe
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }
}
