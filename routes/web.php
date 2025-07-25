<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionAcceptController;
use App\Http\Controllers\CashierPaymentMethodController;
use App\Http\Controllers\AdminDashboardController; // ✅ NUEVO

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard principal (detecta automáticamente el rol)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ✅ NUEVAS RUTAS ADMINISTRATIVAS
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    // Dashboard administrativo
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
    
    // Gestión de usuarios
    Route::get('/users', [AdminDashboardController::class, 'users'])
        ->name('admin.users');
    
    // Gestión de transacciones
    Route::get('/transactions', [AdminDashboardController::class, 'transactions'])
        ->name('admin.transactions');
    
    // Configuración del sistema
    Route::get('/settings', [AdminDashboardController::class, 'settings'])
        ->name('admin.settings');
    
    // Exportación de datos
    Route::post('/export', [AdminDashboardController::class, 'export'])
        ->name('admin.export');
});

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
    
    // Rutas para formularios y historial
    Route::get('/transactions/create', [TransactionController::class, 'create'])
        ->name('transactions.create');
    
    Route::post('/transactions/bank-info', [TransactionController::class, 'getBankInfo'])
        ->name('transactions.bank-info');
    
    Route::post('/transactions/preview-commissions', [TransactionController::class, 'previewCommissions'])
        ->name('transactions.preview-commissions');
    
    Route::get('/transactions/history', [TransactionController::class, 'history'])
        ->name('transactions.history');
    
    Route::post('/transactions', [TransactionController::class, 'store'])
        ->name('transactions.store');
});

// Métodos de pago de cajeros
Route::middleware('auth')->prefix('cashier')->group(function () {
    // Gestión de métodos de pago
    Route::get('/payment-methods', [CashierPaymentMethodController::class, 'index'])
        ->name('cashier.payment-methods');
    
    Route::post('/payment-methods', [CashierPaymentMethodController::class, 'store'])
        ->name('cashier.payment-methods.store');
    
    Route::put('/payment-methods/{paymentMethod}', [CashierPaymentMethodController::class, 'update'])
        ->name('cashier.payment-methods.update');
    
    Route::delete('/payment-methods/{paymentMethod}', [CashierPaymentMethodController::class, 'destroy'])
        ->name('cashier.payment-methods.destroy');
    
    Route::post('/payment-methods/{paymentMethod}/make-primary', [CashierPaymentMethodController::class, 'makePrimary'])
        ->name('cashier.payment-methods.make-primary');
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