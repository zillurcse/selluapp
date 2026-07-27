<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('dropshipping_url')->nullable()->after('is_dropshipping');
            $table->string('dropshipping_sku', 100)->nullable()->after('dropshipping_url');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['dropshipping_url', 'dropshipping_sku']);
        });
    }
};
