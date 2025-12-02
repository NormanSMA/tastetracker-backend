<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // 1. Tarjetas de Resumen (Ventas, Pedidos, etc.)
    public function summary(Request $request)
    {
        $user = $request->user();
        $range = $request->input('range', 'today'); // Filtro global para tarjetas

        // Definir fecha inicio
        $startDate = match($range) {
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subDays(30),
            default => Carbon::today(),
        };

        // Query Base
        $ordersQuery = Order::where('status', 'paid')
            ->where('created_at', '>=', $startDate);

        if ($user->role === 'waiter') {
            $ordersQuery->where('waiter_id', $user->id);
        }

        // Calcular métricas
        $totalSales = (clone $ordersQuery)->sum('total');
        $totalOrders = (clone $ordersQuery)->count();

        // Top Product (Global)
        $topProduct = OrderItem::select('product_id', DB::raw('sum(quantity) as total_qty'))
            ->whereHas('order', fn($q) => $q->where('status', 'paid')->where('created_at', '>=', $startDate))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->first();

        // Top 5 Productos
        $topProducts = OrderItem::select('product_id', DB::raw('sum(quantity) as total_qty'))
            ->whereHas('order', fn($q) => $q->where('status', 'paid')->where('created_at', '>=', $startDate))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'name' => $item->product->name,
                'quantity' => $item->total_qty
            ]);

        // Top 3 Meseros (Solo visible si NO es mesero)
        $topWaiters = [];
        if ($user->role !== 'waiter') {
            $topWaiters = User::where('role', 'waiter')
                ->withSum(['orders' => fn($q) => $q->where('status', 'paid')->where('created_at', '>=', $startDate)], 'total')
                ->orderByDesc('orders_sum_total')
                ->limit(3)
                ->get()
                ->map(fn($user) => [
                    'name' => $user->name,
                    'total_sales' => $user->orders_sum_total ?? 0,
                    'photo_url' => $user->photo_url
                ]);
        }

        return response()->json([
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'top_product' => $topProduct ? [
                'name' => $topProduct->product->name,
                'quantity' => $topProduct->total_qty
            ] : null,
            'top_products' => $topProducts,
            'top_waiters' => $topWaiters,
        ]);
    }

    // 2. Gráfico de Ventas (Informe de Ventas)
    public function salesChart(Request $request)
    {
        $range = $request->input('range', 'week'); // week o month
        $days = $range === 'month' ? 30 : 7;
        $startDate = Carbon::today()->subDays($days - 1);

        // Generar fechas vacías
        $dates = collect(range(0, $days - 1))->map(fn($i) =>
            Carbon::today()->subDays($i)->format('Y-m-d')
        )->reverse()->values();

        // Consultar DB
        $query = Order::where('status', 'paid')
            ->whereDate('created_at', '>=', $startDate);

        // Si es mesero, filtrar solo sus ventas
        if ($request->user()->role === 'waiter') {
            $query->where('waiter_id', $request->user()->id);
        }

        $sales = $query->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // Rellenar datos
        $data = $dates->map(fn($date) => $sales->get($date) ?? 0);
        $labels = $dates->map(fn($date) => Carbon::parse($date)->locale('es')->isoFormat('dd D'));

        return response()->json([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Ventas',
                'data' => $data,
                'backgroundColor' => '#10B981',
                'borderRadius' => 4
            ]]
        ]);
    }

    // 3. Gráfico de Categorías
    public function categoryChart(Request $request)
    {
        $range = $request->input('range', 'today'); // today, week, month
        $startDate = match($range) {
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subDays(30),
            default => Carbon::today(),
        };

        $categories = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('orders.status', 'paid')
            ->where('orders.created_at', '>=', $startDate)
            ->select('categories.name', DB::raw('sum(order_items.quantity) as total'))
            ->groupBy('categories.name')
            ->get();

        return response()->json([
            'labels' => $categories->pluck('name'),
            'datasets' => [[
                'data' => $categories->pluck('total'),
                'backgroundColor' => ['#F59E0B', '#3B82F6', '#10B981', '#EC4899', '#6366F1'],
            ]]
        ]);
    }

    // Mantener index() para compatibilidad con frontend existente
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
            $categoryChart = $this->getOldCategoryChart();

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
        $salesChart = $this->getOldSalesChart($isWaiter ? $user->id : null);

        // Category Chart (Siempre Global para ver tendencias)
        $categoryChart = $this->getOldCategoryChart();

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

    private function getOldSalesChart($waiterId = null)
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

    private function getOldCategoryChart()
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
