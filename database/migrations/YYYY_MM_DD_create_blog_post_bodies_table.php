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
        Schema::create('blog_post_bodies', function (Blueprint $table) {
             // This structure handles multilingual post bodies/content
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('locale'); // e.g., 'en', 'ar'
            $table->text('body'); // Store rich text content (HTML/Markdown)
            $table->timestamps();
        });

        // Indexing for faster lookups on post_id and locale
        Schema::table('blog_post_bodies', function (Blueprint $table) {
            $table->index(['post_id', 'locale']);
        });
    }

    /**
     * Seed the application's migrable database reversal.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_bodies');
    }
};
