# Public Translations

Public Blade pages use namespace JSON files under `lang/{locale}` when the text should be editable through the admin translation screen.

Example files:

```text
lang/fr/about.json
lang/en/about.json
lang/ar/about.json
lang/fr/contact.json
lang/en/contact.json
lang/ar/contact.json
```

These files use flat keys:

```json
{
  "about.title": "Qui sommes-nous",
  "about.story_title": "Notre histoire"
}
```

Laravel's normal `__('about.story_title')` helper does not read `lang/fr/about.json` in this project structure. Public controllers should load namespace JSON files through:

```php
use App\Support\Localization\TranslationNamespace;

return view('pages.about', [
    'about' => TranslationNamespace::get('about'),
]);
```

Then Blade should render from the provided array:

```blade
{{ $about['about.story_title'] ?? '' }}
```

For single values outside a view, use:

```php
TranslationNamespace::value('contact.success_message', 'Message sent successfully.');
```

The helper loads the current locale and falls back to `config('app.fallback_locale')` when the current locale file or key is missing.

Rule: do not use `__('namespace.key')` for public namespace JSON files unless a Laravel translation loader is later added for this exact file structure.
