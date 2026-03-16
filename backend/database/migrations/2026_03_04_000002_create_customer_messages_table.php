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
        Schema::create('customer_messages', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade'); // Vendor ID
            $blueprint->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $blueprint->string('sender_name')->nullable();
            $blueprint->string('sender_email')->nullable();
            $blueprint->string('subject')->nullable();
            $blueprint->text('message');
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
        Schema::dropIfExists('customer_messages');
    }
};
