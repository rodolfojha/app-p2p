<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Events\NewTransactionRequest;

class TransactionService
{
    /**
     * Crea una nueva solicitud de transacción y la transmite.
     *
     * @param  \App\Models\User  $initiator
     * @param  float  $amount
     * @param  string  $type
     * @return \App\Models\Transaction
     * @throws \Exception
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