<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Almacena una nueva transacción y devuelve una respuesta JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type'   => 'required|string|in:deposito,retiro',
        ]);

        try {
            $initiator = Auth::user();

            $transaction = $this->transactionService->createRequest(
                $initiator,
                $validated['amount'],
                $validated['type']
            );

            // Si todo va bien, devolvemos una respuesta JSON con la transacción creada.
            return response()->json([
                'success' => true,
                'message' => '¡Solicitud creada con éxito!',
                'transaction' => $transaction
            ]);

        } catch (Exception $e) {
            // Si algo sale mal, devolvemos un error JSON.
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud: ' . $e->getMessage()
            ], 422); // Código de error para entidad no procesable.
        }
    }
}
