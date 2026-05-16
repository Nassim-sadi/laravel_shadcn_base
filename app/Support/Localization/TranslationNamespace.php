<?php

namespace App\Support\Localization;

class TranslationNamespace
{
    public static function get(string $namespace): array
    {
        $fallback = self::read(config('app.fallback_locale', 'en'), $namespace);
        $current = self::read(app()->getLocale(), $namespace);

        return array_replace($fallback, $current);
    }

    public static function value(string $key, ?string $default = null): string
    {
        [$namespace] = explode('.', $key, 2);

        $translations = self::get($namespace);

        return (string) ($translations[$key] ?? $default ?? $key);
    }

    private static function read(string $locale, string $namespace): array
    {
        $path = lang_path("{$locale}/{$namespace}.json");

        if (! is_file($path)) {
            return [];
        }

        $translations = json_decode((string) file_get_contents($path), true);

        return is_array($translations) ? $translations : [];
    }
}
