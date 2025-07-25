<?php

namespace App\Http\Controllers;

use App\Models\CashierPaymentMethod;
use App\Models\AvailableBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class CashierPaymentMethodController extends Controller
{
    /**
     * Mostrar métodos de pago del cajero
     */
    public function index()
    {
        // Solo permitir a cajeros
        if (!Auth::user()->isCashier()) {
            abort(403, 'Solo los cajeros pueden acceder a esta sección');
        }

        $paymentMethods = CashierPaymentMethod::getActiveMethods(Auth::id());
        $availableBanks = AvailableBank::getActiveBanks();

        return view('cashier.payment-methods', compact('paymentMethods', 'availableBanks'));
    }

    /**
     * Crear nuevo método de pago
     */
    public function store(Request $request)
    {
        // Solo permitir a cajeros
        if (!Auth::user()->isCashier()) {
            return response()->json([
                'success' => false,
                'message' => 'Solo los cajeros pueden agregar métodos de pago'
            ], 403);
        }

        $validated = $request->validate([
            'bank_code' => 'required|string|exists:available_banks,code',
            'account_number' => 'required|string|max:50',
            'account_type' => 'required|string',
            'whatsapp_number' => 'required|string|max:20',
            'account_holder_name' => 'required|string|max:100',
            'account_holder_id' => 'required|string|max:20',
            'is_primary' => 'boolean'
        ]);

        try {
            // Verificar que el banco exista y el tipo de cuenta sea válido
            $bank = AvailableBank::getByCode($validated['bank_code']);
            if (!$bank || !in_array($validated['account_type'], $bank->account_types)) {
                throw new Exception('Tipo de cuenta no válido para el banco seleccionado');
            }

            // Verificar si ya existe un método con la misma cuenta
            $existingMethod = CashierPaymentMethod::where('user_id', Auth::id())
                ->where('account_number', $validated['account_number'])
                ->where('bank_code', $validated['bank_code'])
                ->first();

            if ($existingMethod) {
                throw new Exception('Ya tienes registrado este método de pago');
            }

            // Crear el método de pago
            $paymentMethod = CashierPaymentMethod::create([
                'user_id' => Auth::id(),
                'bank_name' => $bank->name,
                'bank_code' => $bank->code,
                'account_number' => $validated['account_number'],
                'account_type' => $validated['account_type'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'account_holder_name' => $validated['account_holder_name'],
                'account_holder_id' => $validated['account_holder_id'],
                'is_active' => true,
                'is_primary' => $validated['is_primary'] ?? false
            ]);

            // Si se marca como principal, actualizar otros
            if ($validated['is_primary'] ?? false) {
                $paymentMethod->makePrimary();
            }

            return response()->json([
                'success' => true,
                'message' => 'Método de pago agregado exitosamente',
                'payment_method' => $paymentMethod->load('bank')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar método de pago: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Actualizar método de pago
     */
    public function update(Request $request, CashierPaymentMethod $paymentMethod)
    {
        // Verificar que el método pertenece al usuario autenticado
        if ($paymentMethod->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar este método de pago'
            ], 403);
        }

        $validated = $request->validate([
            'account_number' => 'required|string|max:50',
            'whatsapp_number' => 'required|string|max:20',
            'account_holder_name' => 'required|string|max:100',
            'account_holder_id' => 'required|string|max:20',
            'is_active' => 'boolean',
            'is_primary' => 'boolean'
        ]);

        try {
            $paymentMethod->update($validated);

            // Si se marca como principal, actualizar otros
            if ($validated['is_primary'] ?? false) {
                $paymentMethod->makePrimary();
            }

            return response()->json([
                'success' => true,
                'message' => 'Método de pago actualizado exitosamente',
                'payment_method' => $paymentMethod->fresh()->load('bank')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar método de pago: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Eliminar método de pago
     */
    public function destroy(CashierPaymentMethod $paymentMethod)
    {
        // Verificar que el método pertenece al usuario autenticado
        if ($paymentMethod->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este método de pago'
            ], 403);
        }

        try {
            $paymentMethod->delete();

            return response()->json([
                'success' => true,
                'message' => 'Método de pago eliminado exitosamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar método de pago: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Establecer como método principal
     */
    public function makePrimary(CashierPaymentMethod $paymentMethod)
    {
        // Verificar que el método pertenece al usuario autenticado
        if ($paymentMethod->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar este método de pago'
            ], 403);
        }

        try {
            $paymentMethod->makePrimary();

            return response()->json([
                'success' => true,
                'message' => 'Método establecido como principal',
                'payment_method' => $paymentMethod->fresh()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al establecer método principal: ' . $e->getMessage()
            ], 422);
        }
    }
}