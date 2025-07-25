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
        Schema::create('cashier_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID del cajero
            $table->string('bank_name'); // Nequi, Daviplata, Bancolombia, etc.
            $table->string('bank_code'); // nequi, daviplata, bancolombia, dale
            $table->string('account_number'); // Número de cuenta o teléfono
            $table->enum('account_type', ['ahorros', 'corriente', 'nequi', 'daviplata']);
            $table->string('whatsapp_number'); // WhatsApp del cajero
            $table->string('account_holder_name'); // Nombre del titular de la cuenta
            $table->string('account_holder_id'); // Identificación del titular
            $table->boolean('is_active')->default(true); // Para activar/desactivar métodos
            $table->boolean('is_primary')->default(false); // Método principal del cajero
            $table->timestamps();

            // Índices
            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_payment_methods');
    }
};