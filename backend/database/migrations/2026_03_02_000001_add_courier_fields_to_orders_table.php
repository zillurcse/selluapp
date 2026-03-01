<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_name')->nullable()->after('tracking_number');
            $table->string('courier_order_id')->nullable()->after('courier_name');
            $table->string('courier_status')->nullable()->after('courier_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier_name', 'courier_order_id', 'courier_status']);
        });
    }
};
