<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'bank_code',
        'account_number',
        'account_type',
        'whatsapp_number',
        'account_holder_name',
        'account_holder_id',
        'is_active',
        'is_primary'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean'
    ];

    /**
     * Relación con el usuario (cajero)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el banco disponible
     */
    public function bank()
    {
        return $this->belongsTo(AvailableBank::class, 'bank_code', 'code');
    }

    /**
     * Obtener métodos de pago activos de un cajero
     */
    public static function getActiveMethods($userId)
    {
        return self::where('user_id', $userId)
                   ->where('is_active', true)
                   ->orderBy('is_primary', 'desc')
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Obtener el método principal de un cajero
     */
    public static function getPrimaryMethod($userId)
    {
        return self::where('user_id', $userId)
                   ->where('is_active', true)
                   ->where('is_primary', true)
                   ->first();
    }

    /**
     * Obtener el primer método activo si no hay principal
     */
    public static function getDefaultMethod($userId)
    {
        $primary = self::getPrimaryMethod($userId);
        
        if ($primary) {
            return $primary;
        }

        return self::where('user_id', $userId)
                   ->where('is_active', true)
                   ->orderBy('created_at', 'desc')
                   ->first();
    }

    /**
     * Verificar si usa billetera digital
     */
    public function isDigitalWallet()
    {
        return in_array($this->account_type, ['nequi', 'daviplata']);
    }

    /**
     * Obtener información bancaria formateada
     */
    public function getBankInfoAttribute()
    {
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
     * Establecer como método principal y desactivar otros
     */
    public function makePrimary()
    {
        // Quitar el flag de principal a otros métodos del mismo usuario
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Establecer este como principal
        $this->update(['is_primary' => true, 'is_active' => true]);
    }
}