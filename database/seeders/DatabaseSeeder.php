<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use App\Models\Category;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear Usuario Administrador
        User::create([
            'name' => 'Norman Smith admin Principal',
            'email' => 'nsma@tastetracker.com',
            'password' => Hash::make('password'), // Contraseña por defecto
            'role' => 'admin',
            'phone' => '8888-8888',
            'is_active' => true,
        ]);

        // 2. Crear un Mesero de prueba
        User::create([
            'name' => 'Antonio Morales',
            'email' => 'anton@tastetracker.com',
            'password' => Hash::make('password'),
            'role' => 'waiter',
            'phone' => '8888-1111',
            'is_active' => true,
        ]);

        // 3. Crear Áreas del Restaurante
        $areas = [
            ['name' => 'Salón Principal', 'description' => 'Área con aire acondicionado'],
            ['name' => 'Terraza', 'description' => 'Área al aire libre para fumadores'],
            ['name' => 'Barra', 'description' => 'Asientos altos cerca de las bebidas'],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }

        // 4. Crear Categorías del Menú
        $categories = [
            ['name' => 'Entradas', 'slug' => 'entradas', 'image' => null],
            ['name' => 'Platos Fuertes', 'slug' => 'platos-fuertes', 'image' => null],
            ['name' => 'Bebidas', 'slug' => 'bebidas', 'image' => null],
            ['name' => 'Postres', 'slug' => 'postres', 'image' => null],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
