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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable();
            $table->string('product_code')->unique()->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable(); // For multiple images
            
            // Pricing
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            
            // Inventory
            $table->integer('stock_qty')->default(0);
            
            // Product Attributes / Flags
            $table->boolean('has_variants')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_special')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_buy_one_get_one')->default(false);
            $table->boolean('is_preorder')->default(false);
            $table->boolean('is_dropshipping')->default(false);
            
            // Discount
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->string('discount_type')->nullable(); // fixed or percent
            
            // Other
            $table->text('note')->nullable();
            $table->string('video')->nullable(); // video file path
            $table->string('thumbnail')->nullable(); // thumbnail image path
            $table->string('video_url')->nullable(); // external video url if needed
            $table->string('status')->default('published'); // published, draft, etc.
            
            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->string('seo_image')->nullable();
            
            // FAQ usually separate but for now nullable json
            $table->json('faqs')->nullable();

            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // Pivot table for multi-category support
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('products');
    }
};
