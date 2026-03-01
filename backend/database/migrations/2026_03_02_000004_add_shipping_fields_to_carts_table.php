<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'temp_user_id')) {
                $table->string('temp_user_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('carts', 'shipping_cost')) {
                $table->decimal('shipping_cost', 20, 2)->default(0)->after('quantity');
            }
            // user_id might already be required, let's make it nullable for guest checkout
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['temp_user_id', 'shipping_cost']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
