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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('file_name');
            $table->string('file_original_name')->nullable();
            $table->string('extension', 10)->nullable();
            $table->string('type', 20)->nullable();
            $table->integer('file_size')->unsigned()->default(0);
            $table->string('file_path')->nullable();
            $table->string('file_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
