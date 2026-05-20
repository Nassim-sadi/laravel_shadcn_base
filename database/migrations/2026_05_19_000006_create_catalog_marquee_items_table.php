<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_marquee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->json('text')->nullable();
            $table->tinyInteger('position')->default(1);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('position');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_marquee_items');
    }
};
