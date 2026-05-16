<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('subject')->change();
            $table->json('body')->change();
        });

        $defaultLocale = config('localization.fallback_locale', 'fr');

        DB::table('email_templates')->orderBy('id')->each(function ($row) use ($defaultLocale) {
            $updates = [];
            foreach (['name', 'subject', 'body'] as $column) {
                if (! is_null($row->$column) && ! isJson($row->$column)) {
                    $updates[$column] = json_encode([$defaultLocale => $row->$column]);
                }
            }
            if (! empty($updates)) {
                DB::table('email_templates')->where('id', $row->id)->update($updates);
            }
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('subject')->change();
            $table->text('body')->change();
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
