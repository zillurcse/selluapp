<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('product_reviews', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade'); // Vendor ID
            $blueprint->foreignId('customer_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('product_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $blueprint->integer('rating');
            $blueprint->text('comment')->nullable();
            $blueprint->string('status')->default('pending'); // pending, approved, rejected
            $blueprint->boolean('is_verified')->default(false);
            $blueprint->decimal('sentiment_score', 3, 2)->nullable();
            $blueprint->string('sentiment_label')->nullable();
            $blueprint->timestamps();
            $blueprint->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('product_reviews');
    }
};
