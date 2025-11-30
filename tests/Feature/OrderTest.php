<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Area;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Mesero puede crear un pedido con items
     */
    public function test_waiter_can_create_order_with_items(): void
    {
        // Crear datos de prueba
        $waiter = User::factory()->create([
            'role' => 'waiter',
            'is_active' => true,
        ]);

        $area = Area::create([
            'name' => 'Salón Principal',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Comidas',
            'slug' => 'comidas',
            'is_active' => true,
        ]);

        $product1 = Product::create([
            'category_id' => $category->id,
            'name' => 'Hamburguesa',
            'slug' => 'hamburguesa',
            'price' => 12.50,
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'category_id' => $category->id,
            'name' => 'Refresco',
            'slug' => 'refresco',
            'price' => 2.50,
            'is_active' => true,
        ]);

        // Crear pedido con 2 items
        $response = $this->actingAs($waiter, 'sanctum')
                         ->postJson('/api/orders', [
                             'area_id' => $area->id,
                             'table_number' => 'Mesa 5',
                             'order_type' => 'dine_in',
                             'notes' => 'Sin cebolla en la hamburguesa',
                             'items' => [
                                 [
                                     'product_id' => $product1->id,
                                     'quantity' => 2,
                                     'notes' => 'Sin cebolla'
                                 ],
                                 [
                                     'product_id' => $product2->id,
                                     'quantity' => 3,
                                     'notes' => null
                                 ]
                             ]
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'table_number',
                         'status',
                         'total',
                         'waiter',
                         'items'
                     ]
                 ]);

        // Validación CRÍTICA: Verificar cálculo del total
        $expectedTotal = ($product1->price * 2) + ($product2->price * 3);
        // = (12.50 * 2) + (2.50 * 3) = 25.00 + 7.50 = 32.50

        $this->assertEquals($expectedTotal, $response->json('data.total'));

        // Verificar que se guardó en base de datos
        $this->assertDatabaseHas('orders', [
            'waiter_id' => $waiter->id,
            'area_id' => $area->id,
            'table_number' => 'Mesa 5',
            'status' => 'pending',
            'total' => 32.50,
        ]);

        // Verificar que se guardaron los items
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product1->id,
            'quantity' => 2,
            'unit_price' => 12.50,
            'subtotal' => 25.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product2->id,
            'quantity' => 3,
            'unit_price' => 2.50,
            'subtotal' => 7.50,
        ]);
    }

    /**
     * Test: Validar que el total se calcula correctamente con múltiples items
     */
    public function test_order_total_is_calculated_correctly(): void
    {
        $waiter = User::factory()->create(['role' => 'waiter', 'is_active' => true]);
        $area = Area::factory()->create();
        $category = Category::factory()->create();

        $product1 = Product::create([
            'category_id' => $category->id,
            'name' => 'Pizza',
            'slug' => 'pizza',
            'price' => 15.99,
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'category_id' => $category->id,
            'name' => 'Ensalada',
            'slug' => 'ensalada',
            'price' => 8.50,
            'is_active' => true,
        ]);

        $response = $this->actingAs($waiter, 'sanctum')
                         ->postJson('/api/orders', [
                             'area_id' => $area->id,
                             'table_number' => 'Mesa 10',
                             'order_type' => 'dine_in',
                             'items' => [
                                 ['product_id' => $product1->id, 'quantity' => 1],
                                 ['product_id' => $product2->id, 'quantity' => 2],
                             ]
                         ]);

        // Cálculo esperado: (15.99 * 1) + (8.50 * 2) = 15.99 + 17.00 = 32.99
        $expectedTotal = 32.99;

        $response->assertStatus(201);
        $this->assertEquals($expectedTotal, $response->json('data.total'));
    }

    /**
     * Test: Crear pedido requiere autenticación
     */
    public function test_creating_order_requires_authentication(): void
    {
        $area = Area::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/orders', [
            'area_id' => $area->id,
            'table_number' => 'Mesa 1',
            'order_type' => 'dine_in',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test: Cambiar estado de pedido funciona correctamente
     */
    public function test_order_status_can_be_updated(): void
    {
        $waiter = User::factory()->create(['role' => 'waiter', 'is_active' => true]);
        $order = Order::factory()->create([
            'waiter_id' => $waiter->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($waiter, 'sanctum')
                         ->patchJson("/api/orders/{$order->id}/status", [
                             'status' => 'preparing'
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Estado del pedido actualizado',
                     'status' => 'preparing'
                 ]);

        // Verificar en BD
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'preparing',
        ]);
    }

    /**
     * Test: Validación de items requeridos
     */
    public function test_order_requires_at_least_one_item(): void
    {
        $waiter = User::factory()->create(['role' => 'waiter', 'is_active' => true]);
        $area = Area::factory()->create();

        $response = $this->actingAs($waiter, 'sanctum')
                         ->postJson('/api/orders', [
                             'area_id' => $area->id,
                             'table_number' => 'Mesa 3',
                             'order_type' => 'dine_in',
                             'items' => [] // Sin items
                         ]);

        $response->assertStatus(422) // Validation error
                 ->assertJsonValidationErrors('items');
    }

    /**
     * Test: Snapshot de precio se guarda correctamente
     */
    public function test_order_item_saves_price_snapshot(): void
    {
        $waiter = User::factory()->create(['role' => 'waiter', 'is_active' => true]);
        $area = Area::factory()->create();
        $category = Category::factory()->create();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 10.00,
            'is_active' => true,
        ]);

        // Crear pedido
        $this->actingAs($waiter, 'sanctum')
             ->postJson('/api/orders', [
                 'area_id' => $area->id,
                 'table_number' => 'Mesa 1',
                 'order_type' => 'dine_in',
                 'items' => [
                     ['product_id' => $product->id, 'quantity' => 1]
                 ]
             ]);

        // Cambiar precio del producto
        $product->update(['price' => 15.00]);

        // Verificar que el OrderItem tiene el precio histórico (snapshot)
        $orderItem = OrderItem::where('product_id', $product->id)->first();
        $this->assertEquals(10.00, $orderItem->unit_price);
        $this->assertNotEquals($product->fresh()->price, $orderItem->unit_price);
    }
}
