<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cryptocurrency',
        'amount',
        'price',
        'type', // buy or sell
        'status', // pending, accepted, completed, cancelled
        'buyer_id',
        'seller_id',
        'payment_method',
        'transaction_hash',
        // ... otros campos
    ];

    // Definir constantes para los estados de la transacción
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // ... otras relaciones o métodos
}