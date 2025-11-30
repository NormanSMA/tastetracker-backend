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
        // 1. Ventas de Hoy (Solo pedidos pagados)
        $todaySales = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'paid')
            ->sum('total');

        // 2. Cantidad de Pedidos Hoy
        $todayOrdersCount = Order::whereDate('created_at', Carbon::today())->count();

        // 3. Productos Más Vendidos (Top 5)
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product:id,name') // Solo traer el nombre
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->total_quantity,
                ];
            });

        // 4. Empleados con Mayor Facturación (Top 3 Meseros)
        $topWaiters = Order::select('waiter_id', DB::raw('SUM(total) as total_sales'))
            ->where('status', 'paid') // Solo contar ventas reales
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

        return response()->json([
            'today_sales' => $todaySales,
            'today_orders' => $todayOrdersCount,
            'top_products' => $topProducts,
            'top_waiters' => $topWaiters
        ]);
    }
}
