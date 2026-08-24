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
        // NOTE: try/catch inside a Schema::table() closure does not work, because the
        // queued ALTER TABLE statements only execute after the closure returns. Instead
        // we check the actual indexes up front so this is safe on fresh and existing DBs.
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $indexExists = function (string $index) use ($connection, $database): bool {
            return count($connection->select(
                "SELECT 1 FROM information_schema.statistics
                 WHERE table_schema = ? AND table_name = 'business_settings' AND index_name = ? LIMIT 1",
                [$database, $index]
            )) > 0;
        };

        // 1. Drop the incorrect single-column unique index only if it exists
        if ($indexExists('business_settings_type_unique')) {
            Schema::table('business_settings', function (Blueprint $table) {
                $table->dropUnique('business_settings_type_unique');
            });
        }

        // 2. Ensure the composite unique index exists
        if (! $indexExists('business_settings_vendor_type_unique')) {
            Schema::table('business_settings', function (Blueprint $table) {
                $table->unique(['vendor_id', 'type'], 'business_settings_vendor_type_unique');
            });
        }
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
