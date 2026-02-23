<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type'); // credit, debit, refund, fee
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('BDT');
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
