<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'logo_path',
        'account_types',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'account_types' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Obtener bancos activos ordenados
     */
    public static function getActiveBanks()
    {
        return self::where('is_active', true)
                   ->orderBy('sort_order')
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Obtener banco por código
     */
    public static function getByCode($code)
    {
        return self::where('code', $code)
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Obtener tipos de cuenta formateados para display
     */
    public function getFormattedAccountTypesAttribute()
    {
        $types = [
            'ahorros' => 'Ahorros',
            'corriente' => 'Corriente',
            'nequi' => 'Nequi',
            'daviplata' => 'Daviplata'
        ];
        
        return collect($this->account_types)->map(function($type) use ($types) {
            return [
                'value' => $type,
                'label' => $types[$type] ?? ucfirst($type)
            ];
        });
    }

    /**
     * Verificar si el banco es una billetera digital
     */
    public function isDigitalWallet()
    {
        return in_array($this->code, ['nequi', 'daviplata']);
    }

    /**
     * Verificar si el banco es tradicional
     */
    public function isTraditionalBank()
    {
        return in_array($this->code, ['bancolombia', 'dale']);
    }
}