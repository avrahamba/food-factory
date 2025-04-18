<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TestController;
use App\Http\Middleware\CorsMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware([CorsMiddleware::class])->prefix('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('orderItems', OrderItemController::class);
    Route::put('orders/{orderId}/items/{orderItemId}', [OrderItemController::class, 'update']);
    Route::post('orders/{orderId}/items', [OrderItemController::class, 'store']);
    Route::put('orders/{orderId}/items', [OrderItemController::class, 'updateByProduct']);
});
