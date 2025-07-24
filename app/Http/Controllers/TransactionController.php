<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Services\CommissionService;
use App\Models\CommissionSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class TransactionController extends Controller
{
    protected $transactionService;
    protected $commissionService;

    public function __construct(TransactionService $transactionService, CommissionService $commissionService)
    {
        $this->transactionService = $transactionService;
        $this->commissionService = $commissionService;
    }

    /**
     * ✅ Mostrar formulario de nueva transacción
     */
    public function create()
    {
        // Obtener configuraciones de comisiones
        $depositoSettings = CommissionSettings::getActiveSettings('deposito');
        $retiroSettings = CommissionSettings::getActiveSettings('retiro');

        return view('transactions.create', compact('depositoSettings', 'retiroSettings'));
    }

    /**
     * ✅ Preview de comisiones vía AJAX
     */
    public function previewCommissions(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|string|in:deposito,retiro',
            'commission_type' => 'required|string|in:deduct_from_total,add_to_client'
        ]);

        try {
            $preview = $this->commissionService->previewCommissions(
                $request->amount,
                $request->type,
                $request->commission_type
            );

            if (!$preview) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró configuración de comisiones'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $preview
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular comisiones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Crear nueva transacción con comisiones
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|string|in:deposito,retiro',
            'commission_type' => 'required|string|in:deduct_from_total,add_to_client'
        ]);

        try {
            $initiator = Auth::user();
            
            Log::info('Creando nueva transacción con comisiones', [
                'user_id' => $initiator->id,
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'commission_type' => $validated['commission_type']
            ]);

            // Crear la transacción
            $transaction = $this->transactionService->createRequest(
                $initiator,
                $validated['amount'],
                $validated['type']
            );

            // Calcular y asignar comisiones
            $commissionData = $this->commissionService->calculateAndSetCommissions(
                $transaction,
                $validated['amount'],
                $validated['commission_type']
            );

            Log::info('Transacción creada exitosamente con comisiones', [
                'transaction_id' => $transaction->id,
                'final_amount' => $commissionData['final_amount'],
                'total_commission' => $commissionData['commissions']['total_commission']
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Solicitud creada con éxito!',
                'transaction' => $transaction->load(['initiator', 'participant']),
                'commission_data' => $commissionData
            ]);

        } catch (Exception $e) {
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

    /**
     * ✅ Mostrar historial de transacciones
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        // Filtros
        $status = $request->get('status');
        $type = $request->get('type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Query base
        $query = $user->allTransactions()
                     ->with(['initiator', 'participant'])
                     ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $transactions = $query->paginate(10);

        // Estadísticas
        $stats = [
            'total_transactions' => $user->allTransactions()->count(),
            'completed_transactions' => $user->allTransactions()->where('status', 'completed')->count(),
            'total_volume' => $user->allTransactions()->where('status', 'completed')->sum('amount'),
            'total_commissions_earned' => $user->getTotalCommissionEarnings(),
        ];

        return view('transactions.history', compact('transactions', 'stats'));
    }
}