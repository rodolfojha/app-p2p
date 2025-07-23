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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiator_id')->constrained('users');
            $table->foreignId('participant_id')->nullable()->constrained('users');
            $table->string('type'); // 'deposit' o 'withdrawal'
            $table->decimal('amount', 15, 2);
            $table->decimal('total_commission', 15, 2)->default(0.00);
            $table->string('status')->default('pending_acceptance'); // Estados: pending_acceptance, accepted, payment_proof_uploaded, completed, cancelled
            $table->string('payment_proof_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
