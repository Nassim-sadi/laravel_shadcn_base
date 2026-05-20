<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_products', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->longText('body')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price_display', 10, 2)->nullable();
            $table->json('badges')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('catalog_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index('is_active');
            $table->index('order');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_products');
    }
};
