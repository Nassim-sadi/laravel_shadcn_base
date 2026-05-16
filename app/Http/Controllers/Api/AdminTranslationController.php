<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class AdminTranslationController extends Controller
{
    public function show(Request $request, string $locale): JsonResponse
    {
        $this->authorizeAdmin($request);
        $this->validateLocale($locale);

        return response()->json([
            'data' => $this->readTranslations($locale),
        ]);
    }

    public function update(Request $request, string $locale): JsonResponse
    {
        $this->authorizeAdmin($request);
        $this->validateLocale($locale);

        $validated = $request->validate([
            'translations' => ['required', 'array'],
            'translations.*' => ['nullable'],
        ]);

        $translations = $validated['translations'];
        foreach ($translations as $key => $value) {
            if (! is_string($key) || $key === '' || is_array($value)) {
                throw ValidationException::withMessages([
                    'translations' => 'Translations must be a flat key/value JSON object.',
                ]);
            }
        }

        ksort($translations);

        File::ensureDirectoryExists(lang_path());
        File::put(
            lang_path("{$locale}.json"),
            json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL
        );

        return response()->json([
            'message' => 'Translations updated successfully.',
            'data' => $translations,
        ]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    private function validateLocale(string $locale): void
    {
        abort_unless(in_array($locale, config('localization.supported_codes'), true), 404);
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
