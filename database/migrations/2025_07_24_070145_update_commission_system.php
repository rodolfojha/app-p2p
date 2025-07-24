<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ Actualizar tabla users para agregar referidos
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'referred_by')) {
                $table->foreignId('referred_by')->nullable()->constrained('users')->onDelete('set null')->after('earnings');
            }
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code')->unique()->nullable()->after('referred_by');
            }
        });

        // ✅ Actualizar tabla transactions para el nuevo sistema de comisiones
        Schema::table('transactions', function (Blueprint $table) {
            // Agregar campos para el tipo de comisión
            $table->enum('commission_type', ['deduct_from_total', 'add_to_client'])->default('deduct_from_total')->after('total_commission');
            
            // Comisiones detalladas por participante
            $table->decimal('admin_commission', 8, 2)->default(0)->after('commission_type');
            $table->decimal('cashier_commission', 8, 2)->default(0)->after('admin_commission');
            $table->decimal('seller_commission', 8, 2)->default(0)->after('cashier_commission');
            $table->decimal('referral_commission', 8, 2)->default(0)->after('seller_commission');
            
            // IDs de los participantes en comisiones
            $table->foreignId('admin_id')->nullable()->constrained('users')->after('referral_commission');
            $table->foreignId('referral_id')->nullable()->constrained('users')->after('admin_id');
        });

        // ✅ Crear tabla para configuración de comisiones
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'deposito' o 'retiro'
            $table->decimal('total_percentage', 5, 2); // Ej: 3.00 para 3%
            $table->decimal('admin_percentage', 5, 2); // Ej: 40.00 para 40% del total
            $table->decimal('cashier_percentage', 5, 2); // Ej: 30.00 para 30% del total
            $table->decimal('seller_percentage', 5, 2); // Ej: 20.00 para 20% del total
            $table->decimal('referral_percentage', 5, 2); // Ej: 10.00 para 10% del total
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ✅ Insertar configuración por defecto
        DB::table('commission_settings')->insert([
            [
                'type' => 'deposito',
                'total_percentage' => 3.00,
                'admin_percentage' => 40.00,
                'cashier_percentage' => 30.00,
                'seller_percentage' => 20.00,
                'referral_percentage' => 10.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'retiro',
                'total_percentage' => 3.00,
                'admin_percentage' => 40.00,
                'cashier_percentage' => 30.00,
                'seller_percentage' => 20.00,
                'referral_percentage' => 10.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
            if (Schema::hasColumn('users', 'referred_by')) {
                $table->dropForeign(['referred_by']);
                $table->dropColumn('referred_by');
            }
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['referral_id']);
            $table->dropColumn([
                'commission_type',
                'admin_commission',
                'cashier_commission', 
                'seller_commission',
                'referral_commission',
                'admin_id',
                'referral_id'
            ]);
        });

        Schema::dropIfExists('commission_settings');
    }
};