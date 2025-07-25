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
        // Esta es la sección principal para la tabla 'users'
        Schema::create('users', function (Blueprint $table) {
            // Columnas por defecto de Laravel
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // --- NUESTRAS COLUMNAS PERSONALIZADAS ---
            $table->string('role')->default('vendedor'); // Roles: admin, vendedor, cajero
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->decimal('earnings', 15, 2)->default(0.00);
            $table->foreignId('referred_by')->nullable()->constrained('users')->onDelete('set null');
            // --- FIN DE NUESTRAS COLUMNAS ---

            $table->rememberToken();
            $table->timestamps();
        });

        // Esta sección es para la tabla de reseteo de contraseñas, déjala como está
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Esta sección es para la tabla de sesiones, déjala como está
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
