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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->index();
            $table->text('value')->nullable();
            $table->text('default_value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, array
            $table->string('name'); // Human readable name
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Whether this setting can be accessed via public API
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};