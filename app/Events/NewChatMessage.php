<?php

namespace App\Events;

use App\Models\TransactionMessage; // ✅ Cambiar a TransactionMessage
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage; // Para generar URLs de imágenes

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $transactionId; // ✅ Cambiar de tradeId a transactionId

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\TransactionMessage  $message
     * @param  int  $transactionId
     * @return void
     */
    public function __construct(TransactionMessage $message, int $transactionId)
    {
        $this->message = $message;
        $this->transactionId = $transactionId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // ✅ Usar transactionId en lugar de tradeId
        return [
            new Channel('transaction.chat.' . $this->transactionId),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'new.message'; // Nombre del evento que escuchará el frontend
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        // ✅ Datos actualizados para TransactionMessage
        return [
            'id' => $this->message->id,
            'transaction_id' => $this->message->transaction_id, // ✅ Cambiar de trade_id
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user->name, // Envía el nombre del usuario
            'content' => $this->message->content,
            'image_url' => $this->message->image_path ? Storage::url($this->message->image_path) : null, // URL pública de la imagen
            'created_at' => $this->message->created_at->diffForHumans(), // Formato legible
        ];
    }
}