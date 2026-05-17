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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            // Using a dedicated translation table for multilingual title and slug
            $table->boolean('published')->default(false); // Use a proper boolean field
            $table->foreignId('user_id')->nullable()->constrained(); // Author
            $table->timestamps();
        });

        Schema::create('blog_post_translations', function (Blueprint $table) {
             // This structure handles multilingual titles/slugs for posts
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('locale'); // e.g., 'en', 'ar'
            $table->string('title');
            $table->string('slug')->unique(); // Unique slug per locale
            $table->primary(['post_id', 'locale']);
        });

        // Many-to-Many pivot tables (Blog Post <-> Category, Blog Post <-> Tag)
        Schema::create('blog_post_category', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->primary(['post_id', 'category_id']);
        });

        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->primary(['post_id', 'tag_id']);
        });

        // Comments Table (Core structure)
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->commentable(); // Optional: if the commenter is logged in
            $table->string('name'); // Guest name
            $table->string('email')->nullable(); // Guest email
            $table->text('body');
            $table->timestamps();
        });

         // Indexing for foreign keys and frequently queried columns (Best Practice)
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    /**
     * Seed the application's migrable database reversal.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_post_category');
        Schema::dropIfExists('blog_post_translations');
        Schema::dropIfExists('blog_posts');
    }
};
