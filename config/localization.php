<?php

$defaultLanguages = [
    ['code' => 'fr', 'name' => 'Français', 'flag' => '🇫🇷', 'direction' => 'ltr'],
    ['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'direction' => 'ltr'],
    ['code' => 'ar', 'name' => 'العربية', 'flag' => '🇩🇿', 'direction' => 'rtl'],
];

$languages = json_decode((string) env('APP_LANGUAGES', json_encode($defaultLanguages)), true);

if (! is_array($languages)) {
    $languages = $defaultLanguages;
}

$languages = collect($languages)
    ->filter(fn ($language) => is_array($language))
    ->map(function (array $language) {
        return [
            'code' => preg_match('/^[a-z]{2}([_-][A-Z]{2})?$/', $language['code'] ?? '') ? $language['code'] : null,
            'name' => trim((string) ($language['name'] ?? '')),
            'flag' => (string) ($language['flag'] ?? ''),
            'direction' => in_array($language['direction'] ?? 'ltr', ['ltr', 'rtl'], true) ? $language['direction'] : 'ltr',
        ];
    })
    ->filter(fn ($language) => $language['code'] && $language['name'])
    ->unique('code')
    ->values()
    ->all();

if ($languages === []) {
    $languages = $defaultLanguages;
}

$codes = array_column($languages, 'code');
$appLocale = env('APP_LOCALE', 'fr');
$appFallbackLocale = env('APP_FALLBACK_LOCALE', 'fr');
$defaultLocale = in_array($appLocale, $codes, true) ? $appLocale : $codes[0];
$fallbackLocale = in_array($appFallbackLocale, $codes, true) ? $appFallbackLocale : $defaultLocale;

return [
    'default_locale' => $defaultLocale,
    'fallback_locale' => $fallbackLocale,
    'languages' => $languages,
    'supported_codes' => $codes,
];
