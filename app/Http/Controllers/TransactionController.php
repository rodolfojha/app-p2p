<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Services\CommissionService;
use App\Models\CommissionSettings;
use App\Models\AvailableBank;
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
     * ✅ Mostrar formulario de nueva transacción (actualizado con bancos)
     */
    public function create()
    {
        // Obtener configuraciones de comisiones
        $depositoSettings = CommissionSettings::getActiveSettings('deposito');
        $retiroSettings = CommissionSettings::getActiveSettings('retiro');
        
        // ✅ Obtener bancos disponibles
        $availableBanks = AvailableBank::getActiveBanks();

        return view('transactions.create', compact('depositoSettings', 'retiroSettings', 'availableBanks'));
    }

    /**
     * ✅ Obtener información de banco vía AJAX
     */
    public function getBankInfo(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|string'
        ]);

        $bank = AvailableBank::getByCode($request->bank_code);

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => 'Banco no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $bank->name,
                'code' => $bank->code,
                'color' => $bank->color,
                'account_types' => $bank->formatted_account_types,
                'is_digital_wallet' => $bank->isDigitalWallet(),
                'is_traditional_bank' => $bank->isTraditionalBank()
            ]
        ]);
    }

    /**
     * ✅ Preview de comisiones (sin cambios)
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
     * ✅ Crear nueva transacción con información bancaria
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|string|in:deposito,retiro',
            'commission_type' => 'required|string|in:deduct_from_total,add_to_client',
            
            // ✅ Nuevos campos bancarios
            'bank_code' => 'required|string|exists:available_banks,code',
            'account_number' => 'required|string|max:50',
            'account_type' => 'required|string',
            'whatsapp_number' => 'required|string|max:20',
            'account_holder_name' => 'required|string|max:100',
            'account_holder_id' => 'required|string|max:20',
        ]);

        try {
            $initiator = Auth::user();
            
            // ✅ Verificar que el banco exista y el tipo de cuenta sea válido
            $bank = AvailableBank::getByCode($validated['bank_code']);
            if (!$bank || !in_array($validated['account_type'], $bank->account_types)) {
                throw new Exception('Tipo de cuenta no válido para el banco seleccionado');
            }
            
            Log::info('Creando nueva transacción con información bancaria', [
                'user_id' => $initiator->id,
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'bank' => $validated['bank_code'],
                'account_type' => $validated['account_type']
            ]);

            // Crear la transacción con información bancaria
            $transaction = $this->transactionService->createRequestWithBankInfo(
                $initiator,
                $validated['amount'],
                $validated['type'],
                [
                    'bank_name' => $bank->name,
                    'bank_code' => $bank->code,
                    'account_number' => $validated['account_number'],
                    'account_type' => $validated['account_type'],
                    'whatsapp_number' => $validated['whatsapp_number'],
                    'account_holder_name' => $validated['account_holder_name'],
                    'account_holder_id' => $validated['account_holder_id'],
                ]
            );

            // Calcular y asignar comisiones
            $commissionData = $this->commissionService->calculateAndSetCommissions(
                $transaction,
                $validated['amount'],
                $validated['commission_type']
            );

            Log::info('Transacción creada exitosamente con información bancaria', [
                'transaction_id' => $transaction->id,
                'bank' => $bank->name,
                'final_amount' => $commissionData['final_amount']
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Solicitud creada con éxito!',
                'transaction' => $transaction->load(['initiator', 'participant']),
                'commission_data' => $commissionData,
                'bank_info' => [
                    'name' => $bank->name,
                    'color' => $bank->color
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error al crear transacción con información bancaria', [
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
     * ✅ Mostrar historial de transacciones (sin cambios)
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