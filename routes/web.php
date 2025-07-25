<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionAcceptController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard principal
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas de transacciones
Route::middleware('auth')->group(function () {
    // Chat de transacciones
    Route::get('/transaction/{transaction}/chat', [TransactionAcceptController::class, 'showChat'])
        ->name('transaction.chat');
    
    // Rutas para chat
    Route::post('/transaction/{transaction}/send-message', [TransactionAcceptController::class, 'sendMessage'])
        ->name('transaction.send-message');
    
    Route::get('/transaction/{transaction}/messages', [TransactionAcceptController::class, 'getMessages'])
        ->name('transaction.messages');
    
    // Acciones de transacciones
    Route::post('/transactions/{transaction}/accept', [TransactionAcceptController::class, 'accept'])
        ->name('transactions.accept');
    
    Route::post('/transaction/{transaction}/payment-sent', [TransactionAcceptController::class, 'markPaymentSent'])
        ->name('transaction.payment-sent');

    Route::post('/transaction/{transaction}/confirm-payment', [TransactionAcceptController::class, 'confirmPayment'])
        ->name('transaction.confirm-payment');
    
    // ✅ NUEVAS RUTAS PARA FORMULARIOS Y HISTORIAL
    // Formulario de nueva transacción
    Route::get('/transactions/create', [TransactionController::class, 'create'])
        ->name('transactions.create');
    
    // ✅ NUEVA: Obtener información de banco vía AJAX
    Route::post('/transactions/bank-info', [TransactionController::class, 'getBankInfo'])
        ->name('transactions.bank-info');
    
    // Preview de comisiones (AJAX)
    Route::post('/transactions/preview-commissions', [TransactionController::class, 'previewCommissions'])
        ->name('transactions.preview-commissions');
    
    // Historial de transacciones
    Route::get('/transactions/history', [TransactionController::class, 'history'])
        ->name('transactions.history');
    
    // Crear transacciones
    Route::post('/transactions', [TransactionController::class, 'store'])
        ->name('transactions.store');
});

// Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';