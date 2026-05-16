# NsBase Core TODO

This is the working checklist for finishing NsBase core before building project-specific modules.

Current direction: use `git subtree` first. Composer package support can come later after the core shape is stable.

## Phase 1: Subtree-Ready Core

- [ ] Keep NsBase in its own repo.
- [ ] Use downstream projects with `packages/nsbase` via `git subtree`.
- [ ] Add or confirm package identity for future Composer use.
- [ ] Document the downstream update command:

```bash
git subtree pull --prefix=packages/nsbase nsbase main --squash
composer update nassim/nsbase
php artisan migrate
```

- [ ] Define which files are core-owned and should not be edited in downstream projects.
- [ ] Define which config, views, routes, and models downstream projects may override.

## Phase 2: Module Toggle Hardening

- [x] Keep module flags in `config/modules.php`.
- [x] Keep `.env.example` module flags in sync.
- [x] Guard sidebar items in `resources/js/composables/use-sidebar.ts`.
- [x] Guard Vue admin routes in `resources/js/router/index.ts`.
- [x] Guard API route groups in `routes/api.php`.
- [x] Guard public Blade routes in `routes/web.php`.
- [x] Avoid blank admin pages when a module is disabled.
- [x] Avoid public links or sitemap URLs for disabled modules.
- [x] Add tests for disabled module behavior.
- [x] Update existing tests so each test enables the modules it expects.

## Phase 3: Public Translation System

- [x] Treat `lang/{locale}/{namespace}.json` as the source for editable static UI text.
- [x] Use `App\Support\Localization\TranslationNamespace` for public Blade namespace JSON.
- [x] Replace any public `__('namespace.key')` calls that point to namespace JSON files.
- [x] Add fallback behavior for missing locale files.
- [x] Add a small developer note explaining the public translation pattern.
- [x] Confirm About and Contact render translated text in `fr`, `en`, and `ar`.

## Phase 4: Admin Translation Polish

- [ ] Confirm translation namespace tabs load all available `lang/{locale}/*.json` files.
- [ ] Confirm `TRANSLATION_NAMESPACES` filters initial visible namespaces.
- [ ] Confirm UI dropdown hidden namespace state works per locale.
- [ ] Show missing keys/fallbacks clearly in the admin editor.
- [ ] Confirm save/update behavior preserves JSON formatting safely.
- [ ] Verify Arabic RTL rendering in the translation admin.

## Phase 5: Core CRUD Quality Pass

Review each built-in module for validation, permissions, empty states, delete behavior, activity logging, tests, and module toggle behavior.

- [ ] Services
- [ ] Projects
- [ ] Testimonials
- [ ] FAQs
- [ ] Media
- [ ] Contact messages
- [ ] Email templates
- [ ] Activity logs
- [ ] Settings
- [ ] Users
- [ ] Roles and permissions

## Phase 6: Roles and Permissions

- [ ] Define default core roles.
- [ ] Define permissions per module.
- [ ] Ensure permission seeders can be safely re-run.
- [ ] Hide admin UI actions when the user lacks permission.
- [ ] Deny backend actions through policies or explicit permission checks.
- [ ] Remove or avoid dead permissions for disabled or unbuilt modules.

## Phase 7: Settings System

- [ ] Confirm general/company settings.
- [ ] Confirm SEO defaults.
- [ ] Confirm mail settings.
- [ ] Confirm business contact settings.
- [ ] Confirm appearance basics.
- [ ] Confirm social links.
- [ ] Keep settings centralized and reusable.
- [ ] Avoid hardcoding business settings directly in views/controllers.

## Phase 8: Media Manager

- [ ] Upload media.
- [ ] Edit media metadata.
- [ ] Delete media with reference checks.
- [ ] Reuse picker modal from modules.
- [ ] Decide whether folder/category support is core.
- [ ] Make media usable by services, projects, catalog, doctors, and future modules.

## Phase 9: Email Templates

- [ ] Confirm contact confirmation template.
- [ ] Confirm admin notification template.
- [ ] Support subject customization.
- [ ] Support body customization.
- [ ] Support variables/placeholders.
- [ ] Support preview.
- [ ] Keep safe fallback defaults.
- [ ] Prepare for future booking status emails.

## Phase 10: Tests

- [x] Make `php artisan test` reliable regardless of local module flags.
- [x] Add helpers for enabling/disabling modules in tests.
- [x] Add disabled-module route tests.
- [x] Add public route hiding tests.
- [x] Add API route disabled tests.
- [x] Add Vue route/sidebar expectations where practical.

## First Work Item

Continue with Phase 4: Admin Translation Polish.

Reason: public Blade pages now use the namespace JSON source, so the admin translation editor should be polished next to manage that source safely.
