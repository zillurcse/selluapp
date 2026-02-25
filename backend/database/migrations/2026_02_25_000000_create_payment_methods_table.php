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
        Schema::create('payment_methods', function (Blueprint $row) {
            $row->id();
            $row->string('name');
            $row->string('slug')->unique();
            $row->string('icon')->nullable();
            $row->text('description')->nullable();
            $row->boolean('is_active')->default(true);
            $row->json('config_fields')->nullable(); // JSON schema for gateway configuration
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
