# NsBase

NsBase is the reusable Laravel + Vue foundation for custom business projects.

It is intended to become the shared core used by project-specific apps such as booking systems, catalog sites, clinic dashboards, and other structured business tools. The core stays boring and explicit: Laravel owns backend logic and APIs, Vue owns admin/client app screens, and Blade owns public SEO pages.

The current roadmap is documented in:

- [Plans/nsbase_project_architecture.html](Plans/nsbase_project_architecture.html)
- [Plans/nsbase_git_subtree_setup.html](Plans/nsbase_git_subtree_setup.html)

## Direction

The target architecture is:

1. NsBase lives as its own reusable core.
2. Each client or product project installs or vendors NsBase.
3. Core fixes are pulled into each project through one controlled update path.
4. Project-specific modules stay outside the core unless they are truly reusable.

The preferred long-term approach is a private Composer package:

```bash
composer require nassim/nsbase
composer update nassim/nsbase
```

The practical fallback while the package is still taking shape is `git subtree`:

```bash
git subtree add --prefix=packages/nsbase nsbase main --squash
git subtree pull --prefix=packages/nsbase nsbase main --squash
composer update nassim/nsbase
php artisan migrate
```

Do not modify NsBase core files inside a downstream project. Extend behavior with config, published views, project-level models/controllers, events/listeners, or by fixing NsBase in the NsBase repo itself.

## Core Scope

NsBase core should contain reusable foundation features:

- Auth, registration, login, profile, and API authentication.
- Users, roles, and permissions.
- Admin layout and shared Vue admin components.
- Settings grouped by domain.
- Media manager and uploads.
- Email templates with variables and preview support.
- Contact messages.
- Custom queue-based activity logging.
- Public Blade layout and basic SEO pages.
- Translation infrastructure for static UI text.
- Reusable model traits such as translated attributes and SEO fields.
- Module flags for turning built-in or optional groups on and off.

Project-specific features should live in the project:

- Booking business rules.
- Clinic appointment logic.
- Catalog product logic.
- Custom public pages.
- Custom migrations, routes, controllers, and Vue screens.
- Client-specific integrations.

## Current Built-In Modules

These modules are controlled by `.env` flags through `config/modules.php`.

Built-in content and system modules:

- `services`
- `projects`
- `testimonials`
- `faqs`
- `media`
- `contact`
- `email_templates`
- `activity_logs`
- `translations`

Optional add-on modules:

- `catalog`
- `booking`
- `blog`

Example:

```env
MODULE_SERVICES=false
MODULE_PROJECTS=true
MODULE_TESTIMONIALS=true
MODULE_FAQS=true
MODULE_MEDIA=true
MODULE_CONTACT=true
MODULE_EMAIL_TEMPLATES=true
MODULE_ACTIVITY_LOGS=true
MODULE_TRANSLATIONS=true
MODULE_CATALOG=false
MODULE_BOOKING=false
MODULE_BLOG=false
```

When adding or changing a module flag, keep these three places in sync:

- Sidebar: `resources/js/composables/use-sidebar.ts`
- Vue router: `resources/js/router/index.ts`
- API routes: `routes/api.php`

Public Blade routes should also be guarded in `routes/web.php` when the module has public pages.

## Module Roadmap

### Booking

Booking adds appointment scheduling on top of the core.

Tables planned:

- `staff`
- `availability_rules`
- `time_slots`
- `bookings`
- `booking_extras`
- `booking_status_logs`

Statuses:

- `pending`
- `confirmed`
- `rescheduled`
- `cancelled`
- `completed`
- `no_show`

Build phases:

1. Service and staff setup.
2. Availability rules and exception dates.
3. Public Blade booking form.
4. Admin Vue booking management.
5. Calendar view.
6. Optional client portal.

Core principle: generate slots from availability rules on demand. Do not pre-generate unnecessary slot rows unless the project truly needs it.

### Catalog

Catalog is a display and quote-request module, not e-commerce.

Tables planned:

- `catalog_categories`
- `catalog_products`
- `catalog_product_images`
- `catalog_attributes`
- `catalog_attribute_values`
- `catalog_brands` optional
- `quote_requests`

Public pages planned:

- `/catalog`
- `/catalog/{category}`
- `/catalog/product/{slug}`
- `/quote`

Build phases:

1. Categories and products CRUD.
2. Attribute system.
3. Public catalog with query-param filters.
4. Quote request flow.

Boundary: no cart and no payment. If checkout becomes required, treat it as a separate scope upgrade or use WooCommerce.

### Clinic

Clinic is a project-specific module built from the same booking ideas, with medical roles and appointment flow.

Tables planned:

- `doctors`
- `doctor_services`
- `patients`
- `appointments`
- `appointment_status_logs`
- `schedules`
- `schedule_exceptions`

Roles:

- `admin`
- `doctor`
- `receptionist`
- `patient` optional

Appointment lifecycle:

- `requested`
- `scheduled`
- `confirmed`
- `in_progress`
- `completed`
- `cancelled`
- `no_show`

Build phases:

1. Doctors and services CRUD.
2. Patients CRUD.
3. Schedule and appointments.
4. Doctor dashboard.
5. Optional public booking page.

Core principle: patients are not system users by default. Create patient user accounts only if a portal is required.

## Public Pages

Public SEO pages use Blade under `resources/views`.

Use Blade for:

- Landing pages.
- About/contact pages.
- Public service/project/catalog pages.
- Static or mostly static business pages.

Use Vue only as an island when a public section genuinely needs richer interaction.

Static public text currently uses namespace JSON files:

```text
lang/fr/about.json
lang/en/about.json
lang/ar/about.json
lang/fr/contact.json
lang/en/contact.json
lang/ar/contact.json
```

Public controllers that render these namespace JSON files should use:

```php
App\Support\Localization\TranslationNamespace
```

This keeps the admin translation files and Blade pages speaking the same format.

See [docs/PUBLIC_TRANSLATIONS.md](docs/PUBLIC_TRANSLATIONS.md) for the exact pattern.

## Admin Vue Structure

Admin Vue modules should be organized by feature.

Preferred shape:

```text
resources/js/admin/views/services/Index.vue
resources/js/admin/views/services/partials/Create.vue
resources/js/admin/views/services/partials/Edit.vue
resources/js/admin/views/services/partials/Form.vue
```

Shared components belong in:

```text
resources/js/admin/components/
```

Do not place feature-specific components in a global shared folder.

## Activity Logging

NsBase uses a custom activity log, not Spatie Activity Log.

Preferred usage:

```php
activity_log('product.created', [
    'product_id' => $product->id,
    'user_id' => auth()->id(),
]);
```

The logger should remain reusable and queue-based so it does not slow down the main request.

## Development

Install dependencies:

```bash
composer install
npm install
```

Create the environment file and app key:

```bash
cp .env.example .env
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate
```

Start local development:

```bash
php artisan serve
npm run dev
```

Build frontend assets:

```bash
npm run build
```

Run tests:

```bash
php artisan test
```

If a module is disabled through `.env`, tests that expect that module's routes may need module-specific setup or expectations.

## Design Rules

- Prefer explicit Laravel patterns.
- Use FormRequest classes for backend validation when adding new backend features.
- Use API Resources when shaping complex JSON responses.
- Keep public SEO pages in Blade.
- Keep admin/client app experiences in Vue.
- Use Tailwind CSS.
- Avoid Filament, Livewire, Inertia, and Spatie Activity Log unless explicitly requested.
- Avoid building a custom CMS or page builder.
- Prefer small, maintainable patches over large rewrites.

## Theme System

Admin themes are defined as JSON files in `resources/js/themes/` and applied at runtime via CSS variables.

### Adding a new theme

1. **Create a JSON file** in `resources/js/themes/` (e.g. `my-theme.json`):

```json
{
  "id": "my-theme",
  "name": "My Theme",
  "colors": {
    "light": { ... },
    "dark": { ... }
  },
  "fonts": {
    "sans": "'Inter', ui-sans-serif, system-ui, sans-serif",
    "mono": "'JetBrains Mono', ui-monospace, monospace",
    "serif": "'Playfair Display', ui-serif, Georgia, serif"
  }
}
```

2. **Register it** in `resources/js/lib/themes.ts`:

```ts
import myTheme from '@/themes/my-theme.json'

export const themes: Theme[] = [
  amethystHaze as Theme,
  myTheme as Theme,
]
```

3. **Done** — the theme automatically appears in the admin theme customizer dropdown.

### Quick reference

- **`id`** — unique identifier, used in localStorage persistence
- **`name`** — display name shown in the UI
- **`colors.light`** — CSS variables for light mode
- **`colors.dark`** — CSS variables for dark mode
- **`fonts`** (optional) — `sans`, `mono`, `serif` font family stacks
- All color values should use `oklch()` format for consistency with Tailwind v4
- Font values are standard CSS font-family strings

### Local fonts

Fonts are stored locally in `public/fonts/` and declared via `@font-face` in `resources/js/assets/index.css`.

To add a new font:

1. Place WOFF2 files in `public/fonts/<font-name>/`
2. Add `@font-face` declarations to `index.css`:

```css
@font-face {
  font-family: 'My Font';
  src: url('/fonts/my-font/my-font-latin.woff2') format('woff2');
  font-weight: 100 900;
  font-style: normal;
  font-display: swap;
}
```

3. Reference it in a theme's `fonts` field:

```json
{
  "fonts": {
    "sans": "'My Font', sans-serif"
  }
}
```

Currently bundled fonts:

| Font | Type | Source |
|---|---|---|
| Geist | Sans-serif (variable) | Vercel |
| Geist Mono | Monospace (variable) | Vercel |
| Inter | Sans-serif | Google Fonts |
| JetBrains Mono | Monospace | Google Fonts |
| Source Serif 4 | Serif | Google Fonts |
| Oxanium | Sans-serif | Google Fonts |
| Source Code Pro | Monospace | Google Fonts |

## Versioning Roadmap

When NsBase becomes a Composer package, use semantic versioning:

- `1.x`: stable, no breaking changes.
- `2.x`: breaking changes such as schema changes or renamed APIs.

Downstream projects should pin by major version:

```json
{
  "require": {
    "nassim/nsbase": "^1.0"
  }
}
```

That lets projects receive safe fixes while keeping breaking upgrades deliberate.

## Production Deployment

### Quick Deploy

```bash
# 1. Install dependencies (no dev packages)
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# 2. Set up environment
cp .env.example .env
# Edit .env — see "Production .env Checklist" below
php artisan key:generate

# 3. Database
php artisan migrate --force

# 4. Storage symlink
php artisan storage:link

# 5. Seed essential data
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SettingSeeder

# 6. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Production `.env` Checklist

| Variable | Production Value | Notes |
|----------|-----------------|-------|
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | Never `true` in prod |
| `APP_URL` | `https://yourdomain.com` | Must match your domain |
| `SESSION_SECURE_COOKIE` | `true` | Required for HTTPS |
| `SESSION_DOMAIN` | `.yourdomain.com` | For subdomain sharing |
| `SESSION_ENCRYPT` | `true` | Encrypt session cookies |
| `SANCTUM_STATEFUL_DOMAINS` | `yourdomain.com` | Comma-separated |
| `TRUSTED_PROXIES` | `*` or specific IPs | `*` if behind Cloudflare |
| `LOG_STACK` | `daily` | Auto-rotates logs |
| `LOG_LEVEL` | `error` | Reduce noise |
| `API_RATE_LIMIT` | `60` | Requests per minute |
| `MAIL_*` | Your SMTP settings | Required for email verification |

### Server Configuration

**Apache** — `.htaccess` is pre-configured with security headers (CSP, X-Frame-Options, nosniff, etc.), compression, cache headers, and file protection.

**Nginx** — see `deploy/PRODUCTION_CHECKLIST.md` for a full server block with security headers.

**Queue Worker** — copy `deploy/supervisor/laravel-worker.conf` to `/etc/supervisor/conf.d/`, update the path, then:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

**Cron** — add to crontab (`crontab -e`):

```bash
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

### File Permissions

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Rollback

```bash
php artisan down
# Fix the issue or rollback code
git checkout <previous-commit>
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize
php artisan up
```

### Security Features

The following are pre-configured for production:

- **CSRF protection** via Sanctum's stateful cookie authentication
- **XSRF token** auto-attached to API requests
- **Rate limiting** on login (5/min), register (3/min), and all API endpoints (60/min configurable)
- **Email verification** enforced on all authenticated API routes
- **Security headers** — CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy
- **Trusted proxy** support for Cloudflare/reverse proxies
- **Error sanitization** — stack traces hidden when `APP_DEBUG=false`
- **Protected deletes** — system settings, email templates, and roles cannot be deleted
- **Authorization policies** on every controller action
- **Soft deletes** on content models for recovery

See `deploy/PRODUCTION_CHECKLIST.md` for the full deployment guide.
