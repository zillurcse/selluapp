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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['flash_sale', 'flat_discount', 'buy_x_get_y', 'bundle'])->default('flash_sale');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('target', ['all', 'selected', 'categories'])->default('all');
            $table->json('target_ids')->nullable();
            $table->decimal('discount_value', 10, 2);
            $table->enum('discount_unit', ['percentage', 'fixed'])->default('percentage');
            $table->string('banner')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
