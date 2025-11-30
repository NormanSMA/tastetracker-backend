<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFactory extends Factory
{
    protected $model = Area::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Salón Principal', 'Terraza', 'Barra', 'VIP', 'Jardín']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
