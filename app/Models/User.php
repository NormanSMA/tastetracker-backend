<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <--- IMPORTANTE: Agrega esto

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <--- Y esto

    protected $fillable = [
        'name',
        'email',
        'photo',     // <--- AGREGAR AQUÍ
        'password',
        'role',      // Nuevo
        'phone',     // Nuevo
        'is_active', // Nuevo
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relación: Un empleado puede tener muchos pedidos atendidos
    public function orders()
    {
        return $this->hasMany(Order::class, 'waiter_id');
    }
}
