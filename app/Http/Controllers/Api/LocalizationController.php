<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class LocalizationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'languages' => config('localization.languages'),
            'default_locale' => config('localization.default_locale'),
            'fallback_locale' => config('localization.fallback_locale'),
            'enabled_translation_namespaces' => config('localization.enabled_translation_namespaces'),
        ]);
    }

    public function translations(string $locale): JsonResponse
    {
        abort_unless(in_array($locale, config('localization.supported_codes'), true), 404);

        return response()->json($this->readTranslations($locale));
    }

    private function readTranslations(string $locale): array
    {
        $dir = lang_path($locale);

        if (! File::isDirectory($dir)) {
            return [];
        }

        $translations = [];

        foreach (File::files($dir) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $contents = json_decode((string) File::get($file->getPathname()), true);

            if (is_array($contents)) {
                $translations = array_merge($translations, $contents);
            }
        }

        return $translations;
    }
}
