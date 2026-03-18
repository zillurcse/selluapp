<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            // 1. Drop the incorrect single-column unique index if it exists
            // We use a try-catch or check indexes to be safe across environments
            $logicalName = 'business_settings_type_unique';
            
            // In Laravel, we can drop by name
            try {
                $table->dropUnique($logicalName);
            } catch (\Exception $e) {
                // Ignore if it doesn't exist
            }

            // 2. Ensure the composite unique index exists
            // The original migration had: $table->unique(['vendor_id', 'type']);
            // If it already exists, adding it again might fail, so we check or just wrap in try-catch
            try {
                $table->unique(['vendor_id', 'type'], 'business_settings_vendor_type_unique');
            } catch (\Exception $e) {
                // Ignore if it already exists
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            try {
                $table->dropUnique('business_settings_vendor_type_unique');
            } catch (\Exception $e) {}
            
            try {
                $table->unique('type', 'business_settings_type_unique');
            } catch (\Exception $e) {}
        });
    }
};
