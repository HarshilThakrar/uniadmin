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
        Schema::table('page_visits', function (Blueprint $table) {
            $table->index(['is_active', 'created_at'], 'idx_pv_active_created');
            $table->index('url', 'idx_pv_url');
            $table->index('ip_address', 'idx_pv_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropIndex('idx_pv_active_created');
            $table->dropIndex('idx_pv_url');
            $table->dropIndex('idx_pv_ip');
        });
    }
};
