<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type'   => 'required|string|in:deposito,retiro',
        ]);

        try {
            $initiator = Auth::user();
            
            // ✅ Log antes de crear
            Log::info('Creando nueva transacción', [
                'user_id' => $initiator->id,
                'amount' => $validated['amount'],
                'type' => $validated['type']
            ]);

            $transaction = $this->transactionService->createRequest(
                $initiator,
                $validated['amount'],
                $validated['type']
            );

            // ✅ Log después de crear
            Log::info('Transacción creada exitosamente', [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'type' => $transaction->type
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Solicitud creada con éxito!',
                'transaction' => $transaction
            ]);

        } catch (Exception $e) {
            // ✅ Log de errores
            Log::error('Error al crear transacción', [
                'error' => $e->getMessage(),
                'user_id' => $initiator->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud: ' . $e->getMessage()
            ], 422);
        }
    }
}