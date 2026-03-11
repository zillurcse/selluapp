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
        Schema::table('promotions', function (Blueprint $table) {
            // Update enum type or change to string
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE promotions MODIFY type ENUM('flash_sale', 'flat_discount', 'buy_x_get_y', 'bundle', 'category', 'variant') DEFAULT 'flash_sale'");
            }
            
            // Add new Offer Engine fields
            $table->json('rules')->nullable()->after('type')->comment('JSON structured conditions and actions');
            $table->boolean('is_stackable')->default(false)->after('is_active');
            $table->integer('priority')->default(0)->after('is_stackable')->comment('Higher priority executes first');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE promotions MODIFY type ENUM('flash_sale', 'flat_discount', 'buy_x_get_y', 'bundle') DEFAULT 'flash_sale'");
            }
            $table->dropColumn(['rules', 'is_stackable', 'priority']);
        });
    }
};
