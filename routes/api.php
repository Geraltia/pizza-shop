<?php

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);
Route::post('/register', [RegisterController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::post('/decrease', [CartController::class, 'decreaseFromCart']);
        Route::delete('/remove', [CartController::class, 'removeFromCart']);
        Route::get('/', [CartController::class, 'viewCart']);
    });

    Route::prefix('orders')->group(function () {
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
    });

});


Route::middleware(['auth:sanctum',AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('products', [AdminProductController::class, 'products']);
    Route::post('products', [AdminProductController::class, 'addProduct']);
    Route::put('products/{id}', [AdminProductController::class, 'updateProduct']);
    Route::delete('products/{id}', [AdminProductController::class, 'deleteProduct']);

    Route::get('orders', [AdminOrderController::class, 'orders']);
    Route::get('orders/{id}', [AdminOrderController::class, 'orderDetails']);

    Route::post('reports', [AdminReportController::class, 'generateReport']);
});







