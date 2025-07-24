<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionMessage; // ✅ Usar TransactionMessage en lugar de Message
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\TransactionAccepted;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Events\PaymentSent;
use App\Events\PaymentConfirmed;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Storage;

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

        // ✅ Obtener mensajes del chat usando TransactionMessage
        $messages = TransactionMessage::where('transaction_id', $transaction->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // ✅ Marcar mensajes del otro usuario como leídos
        TransactionMessage::where('transaction_id', $transaction->id)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('transaction.chat', compact('transaction', 'messages'));
    }

    // ✅ NUEVO: Enviar mensaje
    public function sendMessage(Request $request, Transaction $transaction)
    {
        // Verificar permisos
        if ($transaction->initiator_id !== Auth::id() && $transaction->participant_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta transacción'
            ], 403);
        }

        $request->validate([
            'content' => 'required_without:image|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        // ✅ Crear mensaje usando TransactionMessage
        $message = TransactionMessage::create([
            'transaction_id' => $transaction->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'image_path' => $imagePath
        ]);

        // Cargar relación del usuario
        $message->load('user');

        // ✅ Broadcast en tiempo real - ajustar para usar transaction_id
        broadcast(new NewChatMessage($message, $transaction->id))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message,
            'image_url' => $imagePath ? Storage::url($imagePath) : null
        ]);
    }

    // ✅ NUEVO: Obtener mensajes
    public function getMessages(Transaction $transaction)
    {
        // Verificar permisos
        if ($transaction->initiator_id !== Auth::id() && $transaction->participant_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta transacción'
            ], 403);
        }

        // ✅ Obtener mensajes usando TransactionMessage
        $messages = TransactionMessage::where('transaction_id', $transaction->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Formatear mensajes para incluir URLs de imágenes
        $formattedMessages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'content' => $message->content,
                'image_path' => $message->image_path,
                'image_url' => $message->image_path ? Storage::url($message->image_path) : null,
                'created_at' => $message->created_at->diffForHumans(),
                'is_mine' => $message->user_id === Auth::id()
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages
        ]);
    }

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