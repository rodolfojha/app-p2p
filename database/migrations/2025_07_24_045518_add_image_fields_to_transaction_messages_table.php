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
        // Agregar campos a la tabla transaction_messages existente
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('content'); // Para comprobantes
            $table->timestamp('read_at')->nullable()->after('image_path'); // Para marcar como leído
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'read_at']);
        });
    }
};