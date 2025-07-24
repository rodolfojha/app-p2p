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
        Schema::table('transaction_messages', function (Blueprint $table) {
            // Modificar la columna content para permitir NULL
            $table->text('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            // Revertir el cambio
            $table->text('content')->nullable(false)->change();
        });
    }
};