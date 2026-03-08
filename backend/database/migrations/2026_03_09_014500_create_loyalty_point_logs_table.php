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
        Schema::create('loyalty_point_logs', function (Blueprint $row) {
            $row->id();
            $row->foreignId('customer_id')->constrained()->onDelete('cascade');
            $row->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $row->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $row->foreignId('pos_sale_id')->nullable()->constrained('pos_sales')->onDelete('set null');
            $row->integer('points');
            $row->string('description')->nullable();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_logs');
    }
};
