<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Seed the application's migrable database.
     */
    public function up(): void
    {
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            // Core tag record - may hold primary data or a reference ID
            $table->string('slug')->unique(); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('blog_tag_translations', function (Blueprint $table) {
             // This structure handles multilingual names/slugs for the tag
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->string('locale'); // e.g., 'en', 'ar'
            $table->string('name');
            $table->primary(['tag_id', 'locale']);
        });
    }

    /**
     * Seed the application's migrable database reversal.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_tag_translations');
        Schema::dropIfExists('blog_tags');
    }
};
