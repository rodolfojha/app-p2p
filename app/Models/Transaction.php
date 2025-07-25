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
        
        // ✅ Nuevos campos bancarios
        'bank_name',
        'bank_code',
        'account_number',
        'account_type',
        'whatsapp_number',
        'account_holder_name',
        'account_holder_id',
        
        // ✅ Campos de comisiones existentes
        'commission_type',
        'admin_commission',
        'cashier_commission',
        'seller_commission',
        'referral_commission',
        'admin_id',
        'referral_id',
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

    /**
     * ✅ Obtener el banco asociado a la transacción
     */
    public function bank()
    {
        return $this->belongsTo(AvailableBank::class, 'bank_code', 'code');
    }

    /**
     * ✅ Verificar si la transacción usa billetera digital
     */
    public function isDigitalWallet()
    {
        return in_array($this->account_type, ['nequi', 'daviplata']);
    }

    /**
     * ✅ Obtener información bancaria formateada
     */
    public function getBankInfoAttribute()
    {
        if (!$this->bank_name) return null;

        return [
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'account_type' => $this->account_type,
            'holder_name' => $this->account_holder_name,
            'holder_id' => $this->account_holder_id,
            'whatsapp_number' => $this->whatsapp_number,
            'is_digital_wallet' => $this->isDigitalWallet()
        ];
    }

    /**
     * ✅ Scope para filtrar por banco
     */
    public function scopeByBank($query, $bankCode)
    {
        return $query->where('bank_code', $bankCode);
    }

    /**
     * ✅ Scope para filtrar por tipo de cuenta
     */
    public function scopeByAccountType($query, $accountType)
    {
        return $query->where('account_type', $accountType);
    }
}