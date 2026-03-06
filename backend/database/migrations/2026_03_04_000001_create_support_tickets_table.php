<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function upstate()
    {
        Schema::create('support_tickets', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade'); // Vendor ID
            $blueprint->foreignId('customer_id')->constrained()->onDelete('cascade');
            $blueprint->string('subject');
            $blueprint->text('description');
            $blueprint->string('priority')->default('medium'); // low, medium, high, urgent
            $blueprint->string('status')->default('open'); // open, in_progress, resolved, closed
            $blueprint->string('source')->default('manual'); // manual, ai_auto, email
            $blueprint->json('ai_metadata')->nullable(); // Store analysis results
            $blueprint->timestamps();
            $blueprint->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
};
