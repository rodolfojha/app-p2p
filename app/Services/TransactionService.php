<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Events\NewTransactionRequest;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    /**
     * ✅ Crear solicitud de transacción con información bancaria
     */
    public function createRequestWithBankInfo(User $initiator, float $amount, string $type, array $bankInfo)
    {
        return DB::transaction(function () use ($initiator, $amount, $type, $bankInfo) {
            
            // Verificar saldo para retiros
            if ($type === 'retiro' && $initiator->balance < $amount) {
                throw new \Exception('Saldo insuficiente para realizar el retiro');
            }

            // Crear la transacción con información bancaria
            $transaction = Transaction::create([
                'initiator_id' => $initiator->id,
                'type' => $type,
                'amount' => $amount,
                'status' => 'pending_acceptance',
                
                // ✅ Información bancaria
                'bank_name' => $bankInfo['bank_name'],
                'bank_code' => $bankInfo['bank_code'] ?? null,
                'account_number' => $bankInfo['account_number'],
                'account_type' => $bankInfo['account_type'],
                'whatsapp_number' => $bankInfo['whatsapp_number'],
                'account_holder_name' => $bankInfo['account_holder_name'],
                'account_holder_id' => $bankInfo['account_holder_id'],
            ]);

            // Para retiros, descontar el monto del saldo inmediatamente
            if ($type === 'retiro') {
                $initiator->decrement('balance', $amount);
                Log::info('Saldo descontado para retiro', [
                    'user_id' => $initiator->id,
                    'amount' => $amount,
                    'new_balance' => $initiator->fresh()->balance
                ]);
            }

            // Cargar las relaciones
            $transaction->load('initiator');

            // Disparar evento para notificar a los cajeros
            broadcast(new NewTransactionRequest($transaction))->toOthers();

            Log::info('Transacción creada con información bancaria', [
                'transaction_id' => $transaction->id,
                'bank' => $bankInfo['bank_name'],
                'account_type' => $bankInfo['account_type']
            ]);

            return $transaction;
        });
    }

    /**
     * ✅ Método original mantenido para compatibilidad
     */
    public function createRequest(User $initiator, float $amount, string $type): Transaction
    {
        $commission = $amount * 0.01; // Ejemplo: comisión del 1%
        $totalDebit = $amount + $commission;

        return DB::transaction(function () use ($initiator, $amount, $commission, $type, $totalDebit) {
            
            $user = User::where('id', $initiator->id)->lockForUpdate()->first();

            if ($user->balance < $totalDebit) {
                throw new Exception("Saldo insuficiente para cubrir el monto y la comisión.");
            }

            $user->decrement('balance', $totalDebit);

            $transaction = Transaction::create([
                'initiator_id'     => $user->id,
                'type'             => $type,
                'amount'           => $amount,
                'total_commission' => $commission,
                'status'           => 'pending_acceptance',
            ]);

            // Precargamos la información del usuario iniciador para enviarla en el evento
            $transaction->load('initiator');

            // ¡Transmitimos el evento a todos los que estén escuchando!
            event(new NewTransactionRequest($transaction));

            // Log para debugging
            \Log::info('Nueva transacción creada y evento disparado', [
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'type' => $type,
                'initiator' => $user->name
            ]);

            return $transaction;
        });
    }
}