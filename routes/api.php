<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/products', [ProductController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
});

Route::middleware(['auth:sanctum',AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('products', [AdminController::class, 'products']);
    Route::post('products', [AdminController::class, 'addProduct']);
    Route::put('products/{id}', [AdminController::class, 'updateProduct']);
    Route::delete('products/{id}', [AdminController::class, 'deleteProduct']);

    Route::get('orders', [AdminController::class, 'orders']);
    Route::get('orders/{id}', [AdminController::class, 'orderDetails']);
    Route::get('generate-report', [AdminController::class, 'generateReport']);
});





