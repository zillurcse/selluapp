<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->timestamp('campaign_start_at')->nullable()->after('is_home');
            $table->timestamp('campaign_end_at')->nullable()->after('campaign_start_at');
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['campaign_start_at', 'campaign_end_at']);
        });
    }
};
