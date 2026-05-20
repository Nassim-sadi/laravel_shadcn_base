<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_attributes', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->string('type')->default('select');
            $table->timestamps();
        });

        Schema::create('catalog_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('catalog_attributes')->cascadeOnDelete();
            $table->json('value');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('catalog_product_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('catalog_products')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('catalog_attributes')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->nullable()->constrained('catalog_attribute_values')->nullOnDelete();
            $table->string('custom_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_product_attribute');
        Schema::dropIfExists('catalog_attribute_values');
        Schema::dropIfExists('catalog_attributes');
    }
};
