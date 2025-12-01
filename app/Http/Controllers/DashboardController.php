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
        $role = $user->role;

        // Inicializar variables
        $todaySales = null;
        $todayOrdersCount = null;
        $topProducts = null;
        $topWaiters = null;
        $salesChart = null;
        $categoryChart = null;

        // 1. Lógica para Kitchen (Cocina)
        if ($role === 'kitchen') {
            // Solo les interesa qué preparar y cuánto trabajo hay
            $todayOrdersCount = Order::whereDate('created_at', Carbon::today())->count();

            // Top productos (Global)
            $topProducts = $this->getTopProducts();

            // Category Chart (Global)
            $categoryChart = $this->getCategoryChart();

            return response()->json([
                'today_sales' => null,
                'today_orders' => $todayOrdersCount,
                'top_products' => $topProducts,
                'top_waiters' => null,
                'sales_chart' => null,
                'category_chart' => $categoryChart
            ]);
        }

        // 2. Lógica para Waiter (Mesero) y Admin
        $isWaiter = $role === 'waiter';

        // Query base para hoy
        $ordersQuery = Order::query()->whereDate('created_at', Carbon::today());
        if ($isWaiter) {
            $ordersQuery->where('waiter_id', $user->id);
        }

        // Métricas básicas
        $todaySales = (clone $ordersQuery)->where('status', 'paid')->sum('total');
        $todayOrdersCount = (clone $ordersQuery)->count();

        // Top Productos (Siempre Global para ver tendencias)
        $topProducts = $this->getTopProducts();

        // Top Meseros (Solo Admin)
        if (!$isWaiter) {
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

        // Sales Chart (Admin: Global, Waiter: Personal)
        $salesChart = $this->getSalesChart($isWaiter ? $user->id : null);

        // Category Chart (Siempre Global para ver tendencias)
        $categoryChart = $this->getCategoryChart();

        return response()->json([
            'today_sales' => $todaySales,
            'today_orders' => $todayOrdersCount,
            'top_products' => $topProducts,
            'top_waiters' => $topWaiters,
            'sales_chart' => $salesChart,
            'category_chart' => $categoryChart
        ]);
    }

    private function getTopProducts()
    {
        return OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function($q) {
                 $q->whereDate('created_at', Carbon::today());
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product:id,name')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => (int) $item->total_quantity,
                ];
            });
    }

    private function getSalesChart($waiterId = null)
    {
        // 1. Generar últimos 7 días (fechas Y-m-d)
        $last7Days = collect(range(0, 6))->map(function($i) {
            return Carbon::today()->subDays($i)->format('Y-m-d');
        })->reverse()->values();

        // 2. Obtener ventas agrupadas por fecha
        $query = Order::where('status', 'paid')
            ->where('created_at', '>=', Carbon::today()->subDays(6));

        if ($waiterId) {
            $query->where('waiter_id', $waiterId);
        }

        $sales = $query->get()->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y-m-d');
        });

        // 3. Fusionar datos (Rellenar ceros)
        $chartData = $last7Days->map(function($date) use ($sales) {
            return $sales->has($date) ? $sales->get($date)->sum('total') : 0;
        });

        $chartLabels = $last7Days->map(fn($date) => ucfirst(Carbon::parse($date)->locale('es')->dayName));

        return [
            'labels' => $chartLabels,
            'data' => $chartData
        ];
    }

    private function getCategoryChart()
    {
        $categoriesData = OrderItem::select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_qty')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'paid')
            ->groupBy('categories.name')
            ->get();

        return [
            'labels' => $categoriesData->pluck('category_name')->toArray(),
            'data' => $categoriesData->pluck('total_qty')->toArray(),
        ];
    }
}
