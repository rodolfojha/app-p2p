<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function broadcastOn()
    {
        // ✅ Cambio principal: Canal específico del vendedor
        return new Channel('user-' . $this->transaction->initiator_id);
    }

    public function broadcastAs()
    {
        return 'transaction-accepted';
    }

    public function broadcastWith()
    {
        return [
            'transaction' => $this->transaction->toArray()
        ];
    }
}