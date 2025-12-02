<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

// --- Rutas Públicas (Cualquiera puede entrar) ---
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/areas', [AreaController::class, 'index']);

// --- Rutas Protegidas (Requieren Token Bearer) ---
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);

    // Profile Management (Usuario edita su propio perfil)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Gestión de Categorías (Admin)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']); // Usar _method: PUT en form-data
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Gestión de Productos (Admin)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Gestión de Pedidos
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']); // Crear pedido
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']); // Actualizar pedido (status completed)
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

    // Dashboard (Modular con filtros independientes)
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/charts/sales', [DashboardController::class, 'salesChart']);
    Route::get('/dashboard/charts/categories', [DashboardController::class, 'categoryChart']);

    // Gestión de Usuarios (Admin)
    Route::apiResource('users', UserController::class);
});
