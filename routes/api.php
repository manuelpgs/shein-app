<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/2fa/verify', [AuthController::class, 'verify2faLogin']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/2fa/generate', [AuthController::class, 'generate2faSecret']);
    Route::post('/2fa/enable', [AuthController::class, 'enable2fa']);
    Route::post('/2fa/disable', [AuthController::class, 'disable2fa']);
    // La verificación 2FA después del login ya tiene su propia ruta
    // Route::post('/2fa/verify', [AuthController::class, 'verify2fa']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('orders', OrderController::class);
    Route::apiResource('clients', ClientController::class);    
});

// Route::middleware(['auth:sanctum', '2fa'])->group(function () {
//     Route::apiResource('orders', OrderController::class);
//     Route::apiResource('clients', ClientController::class);
//     // ... otras rutas protegidas
// });

Route::post('/orders/{order}/payment', [OrderController::class, 'processPayment']);
Route::post('/orders/{order}/tracking', [OrderController::class, 'addTrackingNumber']);
Route::post('/orders/{order}/pickup', [OrderController::class, 'markForPickup']);