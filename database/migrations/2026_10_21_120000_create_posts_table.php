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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->longText('content');
            $table->text('meta_description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->string('author')->default('Unified PT Team');
            $table->json('tags')->nullable();
            $table->string('status')->default('draft')->index(); // 'draft' or 'published'
            $table->string('source')->default('manual'); // 'manual', 'ai', 'ai-edited'
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
