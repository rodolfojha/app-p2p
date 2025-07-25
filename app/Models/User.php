<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
        'earnings',
        'referred_by',
        'referral_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'earnings' => 'decimal:2',
        ];
    }

    /**
     * ✅ Generar código de referido automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateUniqueReferralCode();
            }
        });
    }

    /**
     * ✅ Generar código de referido único
     */
    public static function generateUniqueReferralCode()
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * ✅ Relación: Usuario que refirió a este usuario
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * ✅ Relación: Usuarios referidos por este usuario
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * ✅ NUEVA RELACIÓN: Métodos de pago del cajero
     */
    public function paymentMethods()
    {
        return $this->hasMany(CashierPaymentMethod::class);
    }

    /**
     * ✅ NUEVO: Obtener método de pago principal del cajero
     */
    public function getPrimaryPaymentMethod()
    {
        return $this->paymentMethods()
                    ->where('is_active', true)
                    ->where('is_primary', true)
                    ->first();
    }

    /**
     * ✅ NUEVO: Obtener método de pago por defecto del cajero
     */
    public function getDefaultPaymentMethod()
    {
        $primary = $this->getPrimaryPaymentMethod();
        
        if ($primary) {
            return $primary;
        }

        return $this->paymentMethods()
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->first();
    }

    /**
     * ✅ Transacciones iniciadas
     */
    public function initiatedTransactions()
    {
        return $this->hasMany(Transaction::class, 'initiator_id');
    }

    /**
     * ✅ Transacciones participadas (como cajero)
     */
    public function participatedTransactions()
    {
        return $this->hasMany(Transaction::class, 'participant_id');
    }

    /**
     * ✅ Todas las transacciones relacionadas
     */
    public function allTransactions()
    {
        return Transaction::where('initiator_id', $this->id)
                         ->orWhere('participant_id', $this->id);
    }

    /**
     * ✅ Obtener usuario administrador
     */
    public static function getAdmin()
    {
        return self::where('role', 'admin')->first();
    }

    /**
     * ✅ Verificar si es administrador
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * ✅ Verificar si es cajero
     */
    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    /**
     * ✅ Verificar si es vendedor
     */
    public function isSeller()
    {
        return $this->role === 'vendedor';
    }

    /**
     * ✅ Obtener ganancias totales por comisiones
     */
    public function getTotalCommissionEarnings()
    {
        return Transaction::where('status', 'completed')
                         ->where(function($query) {
                             $query->where('admin_id', $this->id)
                                   ->orWhere('participant_id', $this->id)
                                   ->orWhere('initiator_id', $this->id)
                                   ->orWhere('referral_id', $this->id);
                         })
                         ->sum('total_commission');
    }
}