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
        Schema::create('fraud_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Vendor ID
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            
            $table->integer('risk_score')->default(0); // 0-100 score given by AI
            $table->json('flags')->nullable(); // Array of strings e.g. ["High Velocity", "Proxy IP"]
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reviewer_notes')->nullable();
            
            $table->timestamps();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); // Optional reviewer
            $table->timestamp('reviewed_at')->nullable();
            
            // Allow querying pending fraud checks by vendor efficiently
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_checks');
    }
};
