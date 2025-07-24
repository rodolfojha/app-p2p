<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\Message; // Importar el modelo Message
use App\Events\NewChatMessage; // Importar el evento NewChatMessage
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Para gestionar la subida de archivos

class TradeController extends Controller
{
    // ... otros métodos (index, store, show, update, destroy)

    /**
     * Aceptar una transacción.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Trade  $trade
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, Trade $trade)
    {
        // Verificar si el usuario autenticado es el receptor de la oferta (comprador o vendedor)
        // y si la transacción está en estado 'pending'.
        $user = Auth::user();

        // Asegúrate de que solo el usuario correcto pueda aceptar la transacción.
        // Por ejemplo, si eres el vendedor y alguien quiere comprar tu cripto.
        // O si eres el comprador y el vendedor te ha enviado una oferta.
        if ($trade->status !== Trade::STATUS_PENDING) {
            return response()->json(['message' => 'La transacción no está pendiente de aceptación.'], 400);
        }

        // Lógica para determinar quién puede aceptar la transacción.
        // Esto dependerá de si la transacción es de compra o venta y del rol del usuario.
        // Por ejemplo, si el tipo de trade es 'buy' y el usuario actual es el 'seller_id'
        // O si el tipo de trade es 'sell' y el usuario actual es el 'buyer_id'
        if (($trade->type === 'buy' && $user->id !== $trade->seller_id) ||
            ($trade->type === 'sell' && $user->id !== $trade->buyer_id)) {
            return response()->json(['message' => 'No tienes permiso para aceptar esta transacción.'], 403);
        }

        // Actualizar el estado de la transacción a 'accepted'
        $trade->status = Trade::STATUS_ACCEPTED;
        $trade->save();

        // Aquí puedes añadir lógica adicional:
        // - Notificar a la otra parte que la transacción ha sido aceptada.
        // - Iniciar un temporizador para la finalización de la transacción.
        // - Registrar un evento en el log.

        return response()->json(['message' => 'Transacción aceptada exitosamente.', 'trade' => $trade]);
    }

    /**
     * Enviar un mensaje o un comprobante de pago en el chat de la transacción.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Trade  $trade
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, Trade $trade)
    {
        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validación para imágenes
        ]);

        $user = Auth::user();

        // Asegúrate de que solo los participantes de la transacción puedan enviar mensajes
        if ($user->id !== $trade->buyer_id && $user->id !== $trade->seller_id) {
            return response()->json(['message' => 'No tienes permiso para chatear en esta transacción.'], 403);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Guarda la imagen en storage/app/public/trade_proofs
            // Asegúrate de que 'public' sea un disco configurado en config/filesystems.php
            $imagePath = $request->file('image')->store('trade_proofs', 'public');
        }

        // Crea el mensaje asociado a la transacción
        $message = $trade->messages()->create([
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'image_path' => $imagePath,
        ]);

        // Cargar la relación del usuario para que esté disponible en el broadcast
        $message->load('user');

        // Disparar un evento para broadcasting en tiempo real a los otros participantes
        // El evento NewChatMessage lo crearemos en el siguiente paso
        broadcast(new NewChatMessage($message, $trade->id))->toOthers();

        return response()->json(['message' => 'Mensaje enviado.', 'chat_message' => $message]);
    }

    /**
     * Obtener el historial de mensajes de una transacción.
     *
     * @param  \App\Models\Trade  $trade
     * @return \Illuminate\Http\Response
     */
    public function getMessages(Trade $trade)
    {
        $user = Auth::user();

        // Asegúrate de que solo los participantes puedan ver el chat
        if ($user->id !== $trade->buyer_id && $user->id !== $trade->seller_id) {
            return response()->json(['message' => 'No tienes permiso para ver este chat.'], 403);
        }

        // Obtener los mensajes de la transacción, incluyendo la información del usuario que los envió
        $messages = $trade->messages()->with('user')->orderBy('created_at', 'asc')->get();

        // Formatear las URLs de las imágenes si existen
        $messages->each(function ($message) {
            if ($message->image_path) {
                $message->image_url = Storage::url($message->image_path);
            }
        });

        return response()->json(['messages' => $messages]);
    }
}
