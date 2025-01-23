<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {

    // Order Management
    Route::apiResource('orders', OrderController::class);

    // Payment Management
    Route::get('payments', [PaymentController::class, 'index']);
    Route::post('payments/process', [PaymentController::class, 'processPayment']);
});
