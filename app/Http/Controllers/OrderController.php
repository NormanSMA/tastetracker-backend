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

    // PATCH /api/orders/{id}/status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,paid,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Estado del pedido actualizado',
            'status' => $order->status
        ]);
    }
}
