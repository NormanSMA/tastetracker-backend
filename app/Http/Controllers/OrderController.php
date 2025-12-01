<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        // Traer pedidos con relaciones para evitar N+1
        $orders = Order::with(['waiter', 'customer', 'area', 'items.product'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // 1. Crear la cabecera del pedido (Order)
            // El 'waiter_id' se toma del usuario autenticado (el mesero que usa la app)
            $order = Order::create([
                'user_id' => $request->customer_id,
                'waiter_id' => $request->user()->id,
                'area_id' => $request->area_id,
                'table_number' => $request->table_number,
                'order_type' => $request->order_type,
                'status' => 'pending',
                'notes' => $request->notes,
                'total' => 0, // Lo calcularemos abajo
            ]);

            $grandTotal = 0;

            // 2. Procesar cada item
            foreach ($request->items as $itemData) {
                $product = Product::find($itemData['product_id']);

                // Calcular subtotal usando el precio ACTUAL del producto
                $subtotal = $product->price * $itemData['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $product->price, // Snapshot del precio
                    'subtotal' => $subtotal,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                $grandTotal += $subtotal;
            }

            // 3. Actualizar el total final en la cabecera
            $order->update(['total' => $grandTotal]);

            // Recargar relaciones para la respuesta
            return new OrderResource($order->load(['waiter', 'area', 'items.product']));
        });
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load(['waiter', 'customer', 'area', 'items.product']));
    }

    // PUT /api/orders/{id}
    public function update(Request $request, Order $order)
    {
        // 1. Validar estado
        $request->validate([
            'status' => 'required|string'
        ]);

        // 2. Verificar si ya está pagada/completada
        if ($order->status === 'paid') {
            return response()->json([
                'message' => 'No se puede modificar una orden completada/pagada'
            ], 400);
        }

        // 3. Mapear 'completed' a 'paid'
        $status = $request->status;
        if ($status === 'completed') {
            $status = 'paid';
        }

        // 4. Validar que el estado final sea válido en la BD
        if (!in_array($status, ['pending', 'preparing', 'ready', 'served', 'paid', 'cancelled'])) {
             return response()->json(['message' => 'Estado inválido'], 422);
        }

        // 5. Actualizar
        $order->update(['status' => $status]);

        return new OrderResource($order);
    }

    // PATCH /api/orders/{id}/status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $status = $request->status;
        if ($status === 'completed') {
            $status = 'paid';
        }

        if (!in_array($status, ['pending', 'preparing', 'ready', 'served', 'paid', 'cancelled'])) {
            return response()->json(['message' => 'Estado inválido'], 422);
        }

        $order->update(['status' => $status]);

        return response()->json([
            'message' => 'Estado del pedido actualizado',
            'status' => $order->status
        ]);
    }
}
