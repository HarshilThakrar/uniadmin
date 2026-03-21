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
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['type', 'status', 'location']);
            $table->string('city_name')->nullable()->after('name');
            $table->string('state_name')->nullable()->after('city_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('location')->nullable()->after('name');
            $table->string('type')->nullable()->after('description');
            $table->string('status')->default('Completed')->after('type');
            $table->dropColumn(['city_name', 'state_name']);
        });
    }
};
