<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('position')->nullable()->change();
            $table->json('company')->nullable()->change();
            $table->json('content')->change();
            $table->json('seo_title')->nullable()->change();
            $table->json('seo_description')->nullable()->change();
        });

        $defaultLocale = config('localization.fallback_locale', 'fr');

        DB::table('testimonials')->whereNull('deleted_at')->orderBy('id')->each(function ($row) use ($defaultLocale) {
            $updates = [];
            foreach (['name', 'position', 'company', 'content', 'seo_title', 'seo_description'] as $column) {
                if (! is_null($row->$column) && ! isJson($row->$column)) {
                    $updates[$column] = json_encode([$defaultLocale => $row->$column]);
                }
            }
            if (! empty($updates)) {
                DB::table('testimonials')->where('id', $row->id)->update($updates);
            }
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('position')->nullable()->change();
            $table->string('company')->nullable()->change();
            $table->text('content')->change();
            $table->string('seo_title')->nullable()->change();
            $table->text('seo_description')->nullable()->change();
        });
    }
};

if (! function_exists('isJson')) {
    function isJson($value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
