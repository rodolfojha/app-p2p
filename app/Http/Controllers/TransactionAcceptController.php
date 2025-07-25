<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Models\CashierPaymentMethod;
use App\Services\CommissionService;
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
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

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

        // Verificar que el usuario sea cajero
        if (!Auth::user()->isCashier()) {
            return response()->json([
                'success' => false,
                'message' => 'Solo los cajeros pueden aceptar transacciones'
            ], 403);
        }

        // Para depósitos, verificar que el cajero tenga métodos de pago configurados
        if ($transaction->type === 'deposito') {
            $cashierPaymentMethod = CashierPaymentMethod::getDefaultMethod(Auth::id());
            
            if (!$cashierPaymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes configurar al menos un método de pago antes de aceptar depósitos. Ve a "Mis Métodos de Pago" en tu dashboard.'
                ], 400);
            }

            // Actualizar los datos bancarios de la transacción con los del cajero
            $transaction->update([
                'participant_id' => Auth::id(),
                'status' => 'accepted',
                // Reemplazar datos bancarios con los del cajero
                'bank_name' => $cashierPaymentMethod->bank_name,
                'bank_code' => $cashierPaymentMethod->bank_code,
                'account_number' => $cashierPaymentMethod->account_number,
                'account_type' => $cashierPaymentMethod->account_type,
                'whatsapp_number' => $cashierPaymentMethod->whatsapp_number,
                'account_holder_name' => $cashierPaymentMethod->account_holder_name,
                'account_holder_id' => $cashierPaymentMethod->account_holder_id,
            ]);
        } else {
            // Para retiros, mantener los datos del vendedor
            $transaction->update([
                'participant_id' => Auth::id(),
                'status' => 'accepted'
            ]);
        }

        // Cargar relaciones
        $transaction->load(['initiator', 'participant']);

        // Disparar evento al canal específico del vendedor
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

        // Permitir acceso si está aceptada, payment_sent o completed
        if (!in_array($transaction->status, ['accepted', 'payment_sent', 'completed'])) {
            return redirect()->route('dashboard')->with('error', 'Esta transacción no está en estado válido para el chat');
        }

        // Cargar relaciones
        $transaction->load(['initiator', 'participant']);

        // Obtener mensajes del chat usando TransactionMessage
        $messages = TransactionMessage::where('transaction_id', $transaction->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Marcar mensajes del otro usuario como leídos
        TransactionMessage::where('transaction_id', $transaction->id)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('transaction.chat', compact('transaction', 'messages'));
    }

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

        // Crear mensaje usando TransactionMessage
        $message = TransactionMessage::create([
            'transaction_id' => $transaction->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'image_path' => $imagePath
        ]);

        // Cargar relación del usuario
        $message->load('user');

        // Broadcast en tiempo real
        broadcast(new NewChatMessage($message, $transaction->id))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message,
            'image_url' => $imagePath ? Storage::url($imagePath) : null
        ]);
    }

    public function getMessages(Transaction $transaction)
    {
        // Verificar permisos
        if ($transaction->initiator_id !== Auth::id() && $transaction->participant_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta transacción'
            ], 403);
        }

        // Obtener mensajes usando TransactionMessage
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

        // LÓGICA SEGÚN TIPO DE TRANSACCIÓN
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

        // Disparar evento
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

        // LÓGICA SEGÚN TIPO DE TRANSACCIÓN
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

        // Aquí es donde transferimos los fondos y distribuimos comisiones
        DB::transaction(function () use ($transaction) {
            if ($transaction->type === 'deposito') {
                // DEPÓSITO: Vendedor → Cajero
                // El dinero ya se descontó del vendedor, ahora se acredita al cajero
                $participant = User::find($transaction->participant_id);
                $participant->increment('balance', $transaction->amount);
                
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
                $initiator->increment('balance', $transaction->amount);
            }

            // Cambiar estado a completado PRIMERO
            $transaction->update(['status' => 'completed']);

            // Luego distribuir comisiones
            $this->distributeCommissionsDirectly($transaction);
        });

        // Disparar evento después de completar
        broadcast(new PaymentConfirmed($transaction));

        return response()->json([
            'success' => true,
            'message' => 'Transacción completada exitosamente',
            'transaction' => $transaction
        ]);
    }

    /**
     * Distribuir comisiones directamente sin validar el estado
     */
    private function distributeCommissionsDirectly(Transaction $transaction)
    {
        // Distribuir a administrador
        if ($transaction->admin_id && $transaction->admin_commission > 0) {
            $admin = User::find($transaction->admin_id);
            $admin?->increment('earnings', $transaction->admin_commission);
        }

        // Distribuir a cajero
        if ($transaction->participant_id && $transaction->cashier_commission > 0) {
            $cashier = User::find($transaction->participant_id);
            $cashier?->increment('earnings', $transaction->cashier_commission);
        }

        // Distribuir a vendedor
        if ($transaction->initiator_id && $transaction->seller_commission > 0) {
            $seller = User::find($transaction->initiator_id);
            $seller?->increment('earnings', $transaction->seller_commission);
        }

        // Distribuir a referido
        if ($transaction->referral_id && $transaction->referral_commission > 0) {
            $referral = User::find($transaction->referral_id);
            $referral?->increment('earnings', $transaction->referral_commission);
        }

        \Log::info('Comisiones distribuidas', [
            'transaction_id' => $transaction->id,
            'admin_commission' => $transaction->admin_commission,
            'cashier_commission' => $transaction->cashier_commission,
            'seller_commission' => $transaction->seller_commission,
            'referral_commission' => $transaction->referral_commission,
        ]);
    }
}