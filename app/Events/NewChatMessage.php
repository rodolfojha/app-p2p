<?php

namespace App\Events;

use App\Models\Message;
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
    public $tradeId;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Message  $message
     * @param  int  $tradeId
     * @return void
     */
    public function __construct(Message $message, int $tradeId)
    {
        $this->message = $message;
        $this->tradeId = $tradeId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // El canal privado asegura que solo los usuarios autorizados (que pueden escuchar este canal)
        // reciban el mensaje. Necesitarás configurar la autorización del canal en routes/channels.php
        return [
            new Channel('trade.chat.' . $this->tradeId), // Canal específico para cada transacción
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
        // Puedes personalizar los datos que se envían. Cargar el usuario es útil.
        return [
            'id' => $this->message->id,
            'trade_id' => $this->message->trade_id,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user->name, // Envía el nombre del usuario
            'content' => $this->message->content,
            'image_url' => $this->message->image_path ? Storage::url($this->message->image_path) : null, // URL pública de la imagen
            'created_at' => $this->message->created_at->diffForHumans(), // Formato legible
        ];
    }
}
