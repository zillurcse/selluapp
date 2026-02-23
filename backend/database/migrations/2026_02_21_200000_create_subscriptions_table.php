<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->string('status')->default('active'); // active, expired, cancelled, pending
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, lifetime
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('payment_method')->nullable(); // manual, stripe, sslcommerz
            $table->string('invoice_number')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
