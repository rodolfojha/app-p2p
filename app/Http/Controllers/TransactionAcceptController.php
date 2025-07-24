<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\TransactionAccepted;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Events\PaymentSent;
use App\Events\PaymentConfirmed;

class TransactionAcceptController extends Controller
{
    public function accept(Request $request, Transaction $transaction)
    {
        // Verificar que la transacción esté pendiente
        if ($transaction->status !== 'pending_acceptance') {
            return response()->json([
                'success' => false,
                'message' => 'Esta transacción ya no está disponible'
            ], 400);
        }

        // Verificar que no sea el mismo usuario
        if ($transaction->initiator_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes aceptar tu propia transacción'
            ], 400);
        }

        // Aceptar la transacción
        $transaction->update([
            'participant_id' => Auth::id(),
            'status' => 'accepted'
        ]);

        // Cargar relaciones
        $transaction->load(['initiator', 'participant']);

        // ✅ Disparar evento al canal específico del vendedor
        broadcast(new TransactionAccepted($transaction))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Transacción aceptada exitosamente',
            'transaction' => $transaction
        ]);
    }

    public function showChat(Transaction $transaction)
    {
        // Verificar que el usuario sea participante de la transacción
        if ($transaction->initiator_id !== Auth::id() && $transaction->participant_id !== Auth::id()) {
            abort(403, 'No tienes acceso a esta transacción');
        }

        // ✅ Permitir acceso si está aceptada, payment_sent o completed
        if (!in_array($transaction->status, ['accepted', 'payment_sent', 'completed'])) {
            return redirect()->route('dashboard')->with('error', 'Esta transacción no está en estado válido para el chat');
        }

        // Cargar relaciones
        $transaction->load(['initiator', 'participant']);

        return view('transaction.chat', compact('transaction'));
    }

    // ✅ MÉTODO ACTUALIZADO PARA MANEJAR AMBOS TIPOS DE TRANSACCIÓN
    public function markPaymentSent(Transaction $transaction)
    {
        // Verificar estado
        if ($transaction->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'La transacción no está en estado válido'
            ], 400);
        }

        $currentUserId = Auth::id();

        // ✅ LÓGICA SEGÚN TIPO DE TRANSACCIÓN
        if ($transaction->type === 'deposito') {
            // DEPÓSITO: Solo el vendedor (initiator) puede marcar como enviado
            if ($transaction->initiator_id !== $currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'En depósitos, solo el vendedor puede marcar el pago como realizado'
                ], 403);
            }
            
        } elseif ($transaction->type === 'retiro') {
            // RETIRO: Solo el cajero (participant) puede marcar como enviado
            if ($transaction->participant_id !== $currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'En retiros, solo el cajero puede marcar el pago como realizado'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de transacción no válido'
            ], 400);
        }

        // Cambiar estado
        $transaction->update(['status' => 'payment_sent']);

        // ✅ Disparar evento
        broadcast(new PaymentSent($transaction));

        return response()->json([
            'success' => true,
            'message' => 'Pago marcado como realizado',
            'transaction' => $transaction
        ]);
    }

    // ✅ MÉTODO ACTUALIZADO PARA CONFIRMACIONES
    public function confirmPayment(Transaction $transaction)
    {
        // Verificar estado
        if ($transaction->status !== 'payment_sent') {
            return response()->json([
                'success' => false,
                'message' => 'El pago aún no ha sido marcado como realizado'
            ], 400);
        }

        $currentUserId = Auth::id();

        // ✅ LÓGICA SEGÚN TIPO DE TRANSACCIÓN
        if ($transaction->type === 'deposito') {
            // DEPÓSITO: Solo el cajero (participant) puede confirmar
            if ($transaction->participant_id !== $currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'En depósitos, solo el cajero puede confirmar la recepción'
                ], 403);
            }
            
        } elseif ($transaction->type === 'retiro') {
            // RETIRO: Solo el vendedor (initiator) puede confirmar
            if ($transaction->initiator_id !== $currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'En retiros, solo el vendedor puede confirmar la recepción'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de transacción no válido'
            ], 400);
        }

        // Aquí es donde transferimos los fondos
        DB::transaction(function () use ($transaction) {
            if ($transaction->type === 'deposito') {
                // DEPÓSITO: Vendedor → Cajero
                // El dinero ya se descontó del vendedor, ahora se acredita al cajero
                $participant = User::find($transaction->participant_id);
                $participant->increment('balance', $transaction->amount);
                $participant->increment('earnings', $transaction->total_commission);
                
            } else if ($transaction->type === 'retiro') {
                // RETIRO: Cajero → Vendedor
                // Se descuenta del cajero y se acredita al vendedor
                $participant = User::find($transaction->participant_id);
                $initiator = User::find($transaction->initiator_id);
                
                $totalDebit = $transaction->amount + $transaction->total_commission;
                
                // Verificar que el cajero tenga fondos suficientes
                if ($participant->balance < $totalDebit) {
                    throw new \Exception('El cajero no tiene fondos suficientes');
                }
                
                $participant->decrement('balance', $totalDebit);
                $participant->increment('earnings', $transaction->total_commission);
                $initiator->increment('balance', $transaction->amount);
            }

            // Cambiar estado a completado
            $transaction->update(['status' => 'completed']);
        });

        // ✅ Disparar evento después de completar
        broadcast(new PaymentConfirmed($transaction));

        return response()->json([
            'success' => true,
            'message' => 'Transacción completada exitosamente',
            'transaction' => $transaction
        ]);
    }
}