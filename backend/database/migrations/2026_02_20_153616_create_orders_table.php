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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Vendor ID
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            
            // Order Core
            $table->string('invoice_number')->unique();
            $table->enum('status', ['pending', 'processing', 'courier', 'delivered', 'cancelled', 'returned'])->default('pending');
            $table->enum('type', ['normal', 'pre_order'])->default('normal');
            
            // Financials
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // Payment
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid', 'refunded'])->default('unpaid');
            
            // Shipping Information
            $table->text('shipping_address');
            $table->string('delivery_zone')->nullable();
            $table->foreignId('courier_id')->nullable(); // If you have a couriers table, otherwise use string
            $table->string('tracking_number')->nullable();
            
            // Fraud & AI Protection Fields attached to the order
            $table->integer('risk_score')->default(0); // 0-100 indicating fraud risk
            $table->json('risk_flags')->nullable(); // Array of strings e.g. ["Proxy IP", "High Velocity"]
            $table->boolean('requires_manual_review')->default(false); // Flagged by AI for review
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
