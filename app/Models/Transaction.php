<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'initiator_id',
        'participant_id',
        'type',
        'amount',
        'total_commission',
        'status',
        'payment_proof_path',
    ];

    /**
     * Obtiene el usuario que inició la transacción.
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    /**
     * Obtiene el usuario que participó (aceptó) en la transacción.
     */
    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * ✅ Relación con los mensajes del chat usando TransactionMessage
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TransactionMessage::class);
    }

    /**
     * ✅ Obtener mensajes ordenados por fecha
     */
    public function getMessagesOrdered()
    {
        return $this->messages()->with('user')->orderBy('created_at', 'asc')->get();
    }

    /**
     * ✅ Contar mensajes no leídos para un usuario específico
     */
    public function unreadMessagesForUser($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }
}