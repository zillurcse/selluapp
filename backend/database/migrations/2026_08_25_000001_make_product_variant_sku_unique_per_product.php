<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A variant SKU only needs to be unique within its own product. The original
     * table-wide unique index made two different products unable to share a SKU
     * like "FCB-M", producing a raw "Duplicate entry ... product_variants_sku_unique"
     * 500 on save. Scope the uniqueness to (product_id, sku) instead.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique('product_variants_sku_unique');
            $table->unique(['product_id', 'sku'], 'product_variants_product_sku_unique');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique('product_variants_product_sku_unique');
            $table->unique('sku', 'product_variants_sku_unique');
        });
    }
};
