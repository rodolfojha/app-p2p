<?php

namespace App\Services;

use App\Models\CommissionSettings;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Calcular y distribuir comisiones para una transacción
     */
    public function calculateAndSetCommissions(Transaction $transaction, $amount, $commissionType = 'deduct_from_total')
    {
        // Obtener configuración de comisiones
        $settings = CommissionSettings::getActiveSettings($transaction->type);
        
        if (!$settings) {
            throw new \Exception('No se encontró configuración de comisiones para ' . $transaction->type);
        }

        // Calcular comisiones
        $commissions = $settings->calculateCommissions($amount);
        
        // Obtener participantes
        $admin = User::getAdmin();
        $seller = User::find($transaction->initiator_id);
        $cashier = User::find($transaction->participant_id);
        $referral = $this->getReferralUser($seller, $cashier);

        // Actualizar la transacción con las comisiones calculadas
        $transaction->update([
            'total_commission' => $commissions['total_commission'],
            'commission_type' => $commissionType,
            'admin_commission' => $commissions['admin_commission'],
            'cashier_commission' => $commissions['cashier_commission'],
            'seller_commission' => $commissions['seller_commission'],
            'referral_commission' => $commissions['referral_commission'],
            'admin_id' => $admin?->id,
            'referral_id' => $referral?->id,
        ]);

        return [
            'commissions' => $commissions,
            'final_amount' => $settings->calculateFinalAmount($amount, $commissionType),
            'participants' => [
                'admin' => $admin,
                'seller' => $seller,
                'cashier' => $cashier,
                'referral' => $referral,
            ]
        ];
    }

    /**
     * Distribuir comisiones cuando se completa la transacción
     */
    public function distributeCommissions(Transaction $transaction)
    {
        if ($transaction->status !== 'completed') {
            throw new \Exception('Solo se pueden distribuir comisiones en transacciones completadas');
        }

        DB::transaction(function () use ($transaction) {
            // Distribuir a administrador
            if ($transaction->admin_id && $transaction->admin_commission > 0) {
                $admin = User::find($transaction->admin_id);
                $admin?->increment('earnings', $transaction->admin_commission);
            }

            // Distribuir a cajero
            if ($transaction->participant_id && $transaction->cashier_commission > 0) {
                $cashier = User::find($transaction->participant_id);
                $cashier?->increment('earnings', $transaction->cashier_commission);
            }

            // Distribuir a vendedor
            if ($transaction->initiator_id && $transaction->seller_commission > 0) {
                $seller = User::find($transaction->initiator_id);
                $seller?->increment('earnings', $transaction->seller_commission);
            }

            // Distribuir a referido
            if ($transaction->referral_id && $transaction->referral_commission > 0) {
                $referral = User::find($transaction->referral_id);
                $referral?->increment('earnings', $transaction->referral_commission);
            }
        });

        return true;
    }

    /**
     * Obtener el usuario de referido (quien refirió al vendedor o cajero)
     */
    private function getReferralUser(User $seller, User $cashier = null)
    {
        // Prioridad: referido del vendedor, luego del cajero
        if ($seller && $seller->referred_by) {
            return User::find($seller->referred_by);
        }

        if ($cashier && $cashier->referred_by) {
            return User::find($cashier->referred_by);
        }

        return null;
    }

    /**
     * Obtener configuración de comisiones por tipo
     */
    public function getCommissionSettings($type)
    {
        return CommissionSettings::getActiveSettings($type);
    }

    /**
     * Calcular preview de comisiones para mostrar en el formulario
     */
    public function previewCommissions($amount, $type, $commissionType = 'deduct_from_total')
    {
        $settings = CommissionSettings::getActiveSettings($type);
        
        if (!$settings) {
            return null;
        }

        $commissions = $settings->calculateCommissions($amount);
        $finalAmount = $settings->calculateFinalAmount($amount, $commissionType);

        return [
            'original_amount' => $amount,
            'final_amount' => $finalAmount,
            'total_commission' => $commissions['total_commission'],
            'breakdown' => [
                'admin' => $commissions['admin_commission'],
                'cashier' => $commissions['cashier_commission'],
                'seller' => $commissions['seller_commission'],
                'referral' => $commissions['referral_commission'],
            ],
            'commission_type' => $commissionType,
            'commission_percentage' => $settings->total_percentage,
        ];
    }
}