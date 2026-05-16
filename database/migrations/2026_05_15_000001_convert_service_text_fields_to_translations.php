<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $fields = [
        'title',
        'description',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    public function up(): void
    {
        DB::table('services')
            ->orderBy('id')
            ->select(['id', ...$this->fields])
            ->chunkById(100, function ($services) {
                foreach ($services as $service) {
                    $updates = [];

                    foreach ($this->fields as $field) {
                        $value = $service->{$field};

                        if ($value === null || $this->isJsonObject($value)) {
                            continue;
                        }

                        $updates[$field] = json_encode(['fr' => $value], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }

                    if ($updates !== []) {
                        DB::table('services')->where('id', $service->id)->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        DB::table('services')
            ->orderBy('id')
            ->select(['id', ...$this->fields])
            ->chunkById(100, function ($services) {
                foreach ($services as $service) {
                    $updates = [];

                    foreach ($this->fields as $field) {
                        $value = $service->{$field};

                        if (! $this->isJsonObject($value)) {
                            continue;
                        }

                        $translations = json_decode($value, true);
                        $updates[$field] = $translations['fr'] ?? collect($translations)->first(fn ($translation) => filled($translation));
                    }

                    if ($updates !== []) {
                        DB::table('services')->where('id', $service->id)->update($updates);
                    }
                }
            });
    }

    private function isJsonObject(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) && array_is_list($decoded) === false;
    }
};
