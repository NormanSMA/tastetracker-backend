<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isWaiter = $user->role === 'waiter';

        // 1. Query Base para Ventas y Pedidos
        $ordersQuery = Order::query()->whereDate('created_at', Carbon::today());

        // Si es mesero, filtrar solo sus órdenes
        if ($isWaiter) {
            $ordersQuery->where('waiter_id', $user->id);
        }

        // Calcular métricas
        $todaySales = (clone $ordersQuery)->where('status', 'paid')->sum('total');
        $todayOrdersCount = (clone $ordersQuery)->count();

        // 2. Top Productos (Global para saber qué vender, o filtrado si prefieres)
        // Dejaremos los productos globales para que el mesero sepa qué es popular
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function($q) use ($isWaiter, $user) {
                 $q->whereDate('created_at', Carbon::today());
                 if ($isWaiter) $q->where('waiter_id', $user->id); // Filtra top productos vendidos por ÉL
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product:id,name')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->total_quantity,
                ];
            });

        // 3. Top Meseros (Solo visible para Admin)
        $topWaiters = [];
        if (!$isWaiter) { // Si es Admin
            $topWaiters = Order::select('waiter_id', DB::raw('SUM(total) as total_sales'))
                ->where('status', 'paid')
                ->groupBy('waiter_id')
                ->orderByDesc('total_sales')
                ->with('waiter:id,name')
                ->take(3)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->waiter->name,
                        'sales' => (float) $item->total_sales,
                    ];
                });
        }

        return response()->json([
            'today_sales' => $todaySales,
            'today_orders' => $todayOrdersCount,
            'top_products' => $topProducts,
            'top_waiters' => $topWaiters // Enviará array vacío al mesero
        ]);
    }
}
