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
        ]);
    }

    public function translations(string $locale): JsonResponse
    {
        abort_unless(in_array($locale, config('localization.supported_codes'), true), 404);

        return response()->json($this->readTranslations($locale));
    }

    private function readTranslations(string $locale): array
    {
        $path = lang_path("{$locale}.json");

        if (! File::exists($path)) {
            return [];
        }

        $translations = json_decode((string) File::get($path), true);

        return is_array($translations) ? $translations : [];
    }
}
