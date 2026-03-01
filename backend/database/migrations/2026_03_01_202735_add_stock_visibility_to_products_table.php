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
        Schema::table('products', function (Blueprint $table) {
            $table->string('stock_visibility_state')->default('quantity')->after('stock_qty');
            $table->integer('low_stock_warning_qty')->default(1)->after('stock_visibility_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_visibility_state', 'low_stock_warning_qty']);
        });
    }
};
