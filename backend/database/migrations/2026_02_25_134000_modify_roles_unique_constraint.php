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
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::table($tableNames['roles'], function (Blueprint $table) use ($tableNames) {
            // Drop old unique constraint. According to Spatie it is named roles_name_guard_name_unique
            $table->dropUnique('roles_name_guard_name_unique');
            
            // Add new unique constraint scoped with vendor_id
            $table->unique(['name', 'guard_name', 'vendor_id'], 'roles_name_guard_vendor_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) use ($tableNames) {
            $table->dropUnique('roles_name_guard_vendor_unique');
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        });
    }
};
