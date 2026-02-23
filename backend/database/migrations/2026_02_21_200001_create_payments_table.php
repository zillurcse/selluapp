<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('payment_ref')->nullable()->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('BDT');
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->string('payment_method')->nullable(); // manual, card, bkash, nagad
            $table->string('gateway')->nullable(); // sslcommerz, stripe, manual
            $table->string('gateway_transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
