<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ Agregar campos bancarios a la tabla transactions
        Schema::table('transactions', function (Blueprint $table) {
            // Información bancaria
            $table->string('bank_name')->nullable()->after('type');
            $table->string('bank_code')->nullable()->after('bank_name');
            $table->string('account_number')->nullable()->after('bank_code');
            $table->enum('account_type', ['ahorros', 'corriente', 'nequi', 'daviplata'])->nullable()->after('account_number');
            $table->string('whatsapp_number')->nullable()->after('account_type');
            // Información del titular de la cuenta
            $table->string('account_holder_name')->nullable()->after('whatsapp_number');
            $table->string('account_holder_id')->nullable()->after('account_holder_name');
        });

        // ✅ Crear tabla para configuración de bancos disponibles
        Schema::create('available_banks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nequi, Daviplata, Bancolombia, Dale!
            $table->string('code')->unique(); // nequi, daviplata, bancolombia, dale
            $table->string('logo_path')->nullable(); // Ruta del logo
            $table->json('account_types'); // ['ahorros', 'corriente'] o ['nequi'] para billeteras
            $table->string('color')->default('#6B7280'); // Color del banco para UI
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // ✅ Insertar bancos por defecto
        DB::table('available_banks')->insert([
            [
                'name' => 'Nequi',
                'code' => 'nequi',
                'account_types' => json_encode(['nequi']),
                'color' => '#FF006E',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Daviplata',
                'code' => 'daviplata',
                'account_types' => json_encode(['daviplata']),
                'color' => '#ED1C24',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bancolombia',
                'code' => 'bancolombia',
                'account_types' => json_encode(['ahorros', 'corriente']),
                'color' => '#FFC72C',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dale!',
                'code' => 'dale',
                'account_types' => json_encode(['ahorros', 'corriente']),
                'color' => '#00A651',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'bank_code',
                'account_number',
                'account_type',
                'whatsapp_number',
                'account_holder_name',
                'account_holder_id'
            ]);
        });

        Schema::dropIfExists('available_banks');
    }
};