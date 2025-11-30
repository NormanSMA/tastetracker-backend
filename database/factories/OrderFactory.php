<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'waiter_id' => User::factory()->create(['role' => 'waiter', 'is_active' => true])->id,
            'area_id' => Area::factory(),
            'table_number' => 'Mesa ' . fake()->numberBetween(1, 20),
            'status' => 'pending',
            'order_type' => fake()->randomElement(['dine_in', 'takeaway', 'delivery']),
            'total' => 0,
            'notes' => fake()->sentence(),
        ];
    }
}
