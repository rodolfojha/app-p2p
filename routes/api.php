<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TradeController;

// ... otras rutas

Route::middleware('auth:sanctum')->group(function () {
    // ... tus otras rutas protegidas

    // Rutas para el chat de transacciones
    Route::post('/trades/{trade}/messages', [TradeController::class, 'sendMessage'])->name('trades.sendMessage');
    Route::get('/trades/{trade}/messages', [TradeController::class, 'getMessages'])->name('trades.getMessages');
});
