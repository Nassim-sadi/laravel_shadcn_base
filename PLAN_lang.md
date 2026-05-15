# Shared Multilingual System

## Summary
Build one Laravel-owned translation system for Blade and Vue admin, with French as the default/fallback language. Use `lang/{locale}.json` as the single source of UI translations, expose language metadata from env/config, add an admin translation editor with debounced live saving, and convert content CRUD fields to JSON locale objects with language tabs.

## Key Changes
- Add env/config language settings:
  - `.env.example`: `APP_LOCALE=fr`, `APP_FALLBACK_LOCALE=fr`
  - `APP_LANGUAGES='[{"code":"fr","name":"Français","flag":"🇫🇷","direction":"ltr"},{"code":"en","name":"English","flag":"🇬🇧","direction":"ltr"},{"code":"ar","name":"العربية","flag":"🇩🇿","direction":"rtl"}]'`
  - Add a small `config/localization.php` that parses `APP_LANGUAGES`, validates codes/direction, exposes default/fallback locale, and provides helpers for supported codes.
- Move UI translation ownership to Laravel JSON files:
  - Create `lang/fr.json`, `lang/en.json`, `lang/ar.json`.
  - Migrate existing Vue i18n JSON into those files.
  - Blade uses normal Laravel translation calls like `__('admin.dashboard')`.
  - Vue admin loads messages at runtime from an authenticated-safe/public-safe endpoint, so edits do not require a Vite rebuild.
- Add translation API endpoints:
  - `GET /api/localization` returns languages, default locale, fallback locale.
  - `GET /api/translations/{locale}` returns the JSON for Vue/admin.
  - Admin-only: `GET /api/admin/translations/{locale}` and `PUT /api/admin/translations/{locale}` for the editor.
  - Admin-only means `super_admin` and `admin`.
- Add admin translation editor:
  - New admin page, for example `/admin/translations`.
  - Locale tabs for `fr`, `en`, `ar`.
  - Key/value editor with search, missing-key filter, and debounced live save after roughly 800ms.
  - Backend validates JSON shape before saving, writes pretty JSON, and rejects unknown locales.
  - Missing keys visually show fallback value from French.
- Add translated CRUD content fields using JSON columns:
  - Content scope: Services, Projects, Testimonials, FAQs, Settings labels/descriptions, Email Templates.
  - Exclude Users, Roles, Permissions, Activity Logs, and Contact Messages.
  - Convert current strings into JSON objects under `fr`.
  - Translatable fields:
    - Services: `title`, `description`, `seo_title`, `seo_description`, `seo_keywords`
    - Projects: `title`, `description`, `client`, `seo_title`, `seo_description`, `seo_keywords`
    - Testimonials: `name`, `position`, `company`, `content`, `seo_title`, `seo_description`
    - FAQs: `question`, `answer`, `category`, `seo_title`, `seo_description`
    - Settings: `name`, `description`, and translatable `value` only when setting type is marked translatable later
    - Email Templates: `name`, `subject`, `body`
- Add reusable backend translation handling:
  - A small trait/helper resolves JSON field values by requested locale, then fallback `fr`.
  - API validation accepts translatable fields as objects keyed by configured locale codes.
  - Admin resources return both full translation objects for forms and display strings for tables.
- Add reusable Vue CRUD language tabs:
  - Shared component for locale tabs using env-provided language metadata.
  - Admin create/edit forms show language tabs for translatable fields.
  - Tables display the active admin locale value, falling back to French when missing.
  - User forms keep their current non-translated fields.

## Test Plan
- Backend tests:
  - `GET /api/localization` returns `fr/en/ar`, default `fr`, fallback `fr`.
  - `GET /api/translations/en` falls back to French for missing keys in Vue loader behavior.
  - Admin translation update rejects invalid locale and invalid JSON structure.
  - Non-admin users cannot update translation files.
  - CRUD create/update accepts JSON translation objects and rejects unknown locale keys.
  - CRUD resources return French fallback when selected locale value is missing.
- Frontend/manual tests:
  - Blade homepage renders translated strings from `lang/fr.json`.
  - Admin language switch changes UI text without rebuilding assets.
  - Editing a JSON translation in admin live-saves and appears after reload/refetch.
  - Service/Project/Testimonial/FAQ forms show `fr`, `en`, `ar` tabs.
  - Missing English/Arabic content displays French fallback.
  - RTL language metadata sets `dir="rtl"` when Arabic is active.

## Assumptions
- Initial supported languages are `fr`, `en`, and `ar`.
- French is the default and fallback language.
- Existing plain text content should be preserved as French during migration.
- `lang/{locale}.json` is the canonical source for both Blade and admin UI translations.
- Admin translation file editing is allowed for `admin` and `super_admin`.
