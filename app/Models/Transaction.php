<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}