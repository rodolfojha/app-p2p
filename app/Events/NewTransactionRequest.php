<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTransactionRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        
        \Log::info('🚀 Evento NewTransactionRequest creado', [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
        ]);
    }

    public function broadcastOn()
    {
        return new Channel('public-requests');
    }

    public function broadcastAs()
    {
        return 'new-transaction-request';
    }

    public function broadcastWith()
{
    return [
        'transaction' => [
            'id' => $this->transaction->id,
            'type' => $this->transaction->type,
            'amount' => $this->transaction->amount,
            'created_at' => $this->transaction->created_at->toISOString(),
            'initiator' => [
                'id' => $this->transaction->initiator->id,
                'name' => $this->transaction->initiator->name,
            ]
        ]
    ];
}
}