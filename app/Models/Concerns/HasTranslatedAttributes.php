<?php

namespace App\Models\Concerns;

trait HasTranslatedAttributes
{
    public function translated(string $attribute, ?string $locale = null): ?string
    {
        $value = $this->getAttribute($attribute);

        if (! is_array($value)) {
            return $value;
        }

        $locale ??= request()->string('locale')->toString() ?: app()->getLocale();
        $fallbackLocale = config('localization.fallback_locale', 'fr');

        return $value[$locale]
            ?? $value[$fallbackLocale]
            ?? collect($value)->first(fn ($translation) => filled($translation))
            ?? null;
    }
}
