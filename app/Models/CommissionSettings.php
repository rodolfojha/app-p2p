<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'total_percentage',
        'admin_percentage',
        'cashier_percentage',
        'seller_percentage',
        'referral_percentage',
        'is_active'
    ];

    protected $casts = [
        'total_percentage' => 'decimal:2',
        'admin_percentage' => 'decimal:2',
        'cashier_percentage' => 'decimal:2',
        'seller_percentage' => 'decimal:2',
        'referral_percentage' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Obtener configuración activa por tipo
     */
    public static function getActiveSettings($type)
    {
        return self::where('type', $type)
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Calcular comisiones basadas en un monto
     */
    public function calculateCommissions($amount)
    {
        $totalCommission = ($amount * $this->total_percentage) / 100;
        
        return [
            'total_commission' => $totalCommission,
            'admin_commission' => ($totalCommission * $this->admin_percentage) / 100,
            'cashier_commission' => ($totalCommission * $this->cashier_percentage) / 100,
            'seller_commission' => ($totalCommission * $this->seller_percentage) / 100,
            'referral_commission' => ($totalCommission * $this->referral_percentage) / 100,
        ];
    }

    /**
     * Calcular monto final según tipo de comisión
     */
    public function calculateFinalAmount($amount, $commissionType)
    {
        $totalCommission = ($amount * $this->total_percentage) / 100;
        
        if ($commissionType === 'deduct_from_total') {
            // Restar comisión del total recibido
            return $amount - $totalCommission;
        } else {
            // Agregar comisión al total del cliente
            return $amount + $totalCommission;
        }
    }
}