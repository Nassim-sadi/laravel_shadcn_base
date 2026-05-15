# AGENTS.md

## Project style

This is a Laravel + Vue project.

The preferred architecture is explicit and maintainable:

- Laravel handles backend logic, APIs, validation, auth, queues, permissions, and database work.
- Vue handles admin/client app interfaces.
- Blade is used for public SEO pages and landing pages when needed.
- Avoid magic-heavy abstractions unless they clearly save time and remain easy to maintain.

Do not introduce Filament, Livewire, Inertia, or Spatie Activity Log unless explicitly requested.

Prefer boring, clear code over clever code.

---

## General rules

- Do not scan or edit the whole project unless explicitly asked.
- Work only on the files related to the current task.
- Before touching extra files, explain why they are needed.
- Prefer small patches over large rewrites.
- Do not rename folders or restructure existing modules unless explicitly asked.
- Do not change formatting across unrelated files.
- Do not introduce new packages unless explicitly requested or strongly justified.
- Keep code readable, explicit, and easy to debug.
- Avoid hidden magic, excessive abstraction, and framework tricks.

---

## Laravel backend style

Use explicit Laravel patterns:

- Controllers for API endpoints.
- FormRequest classes for validation.
- Services or actions only when logic is reused or complex.
- Policies or clear permission checks for authorization.
- API Resources when shaping JSON responses is useful.
- Jobs and queues for slow background work.
- Events and listeners only when they simplify the design.

Validation must be handled properly on the backend with FormRequest classes.

Do not rely only on frontend validation.

Recommended backend structure:

- app/Http/Controllers/Admin/
- app/Http/Controllers/Api/
- app/Http/Requests/Admin/
- app/Http/Requests/Api/
- app/Http/Resources/
- app/Models/
- app/Services/
- app/Actions/
- app/Jobs/

---

## Vue admin structure

Admin Vue files must be organized by feature or service.

Each admin service/module should have its own folder.

Example structure:

- resources/js/admin/views/users/Index.vue
- resources/js/admin/views/users/partials/Create.vue
- resources/js/admin/views/users/partials/Edit.vue
- resources/js/admin/views/users/partials/Form.vue

Rules:

- Index.vue holds the main listing/table page.
- partials/Create.vue holds the create modal/page/partial.
- partials/Edit.vue holds the edit modal/page/partial.
- Shared form logic can go in partials/Form.vue.
- Keep module-specific components inside that module folder.
- Do not dump feature-specific components into a global components folder.

For another module, use the same pattern:

- resources/js/admin/views/services/Index.vue
- resources/js/admin/views/services/partials/Create.vue
- resources/js/admin/views/services/partials/Edit.vue
- resources/js/admin/views/services/partials/Form.vue

---

## Shared Vue components

Shared admin components belong in:

- resources/js/admin/components/

Use this folder only for reusable components used by multiple admin modules.

Examples:

- resources/js/admin/components/DataTable.vue
- resources/js/admin/components/ConfirmDialog.vue
- resources/js/admin/components/FormInput.vue
- resources/js/admin/components/ImageUploader.vue
- resources/js/admin/components/Pagination.vue

Do not place feature-specific components in the shared components folder.

---

## Public Blade views

Public SEO pages should use Blade when SEO and shared-hosting performance matter.

Blade views go under:

- resources/views/

Public Blade pages should have their own layout.

Example structure:

- resources/views/layouts/public.blade.php
- resources/views/pages/home.blade.php
- resources/views/pages/services.blade.php
- resources/views/pages/projects.blade.php

Use Blade for:

- Landing pages
- SEO pages
- Public business pages
- Static or mostly static content

Use Vue islands only when a specific interactive section needs Vue.

---

## Client-side Vue app structure

If the project has a client-side Vue app, place its views under:

- resources/js/views/

Example:

- resources/js/views/Index.vue

If the view has multiple internal pieces, use a folder with partials:

- resources/js/views/booking/Index.vue
- resources/js/views/booking/partials/Calendar.vue
- resources/js/views/booking/partials/TimeSlots.vue
- resources/js/views/booking/partials/Confirmation.vue

Do not mix admin Vue views with public/client Vue views.

Keep admin and client apps clearly separated.

---

## Vue validation

Use Vuelidate for Vue form validation.

Rules:

- Frontend validation should improve UX.
- Backend FormRequest validation is still required.
- Keep validation rules close to the form using them.
- Reuse validation helpers only when the same rule appears in multiple modules.
- Do not introduce other validation libraries unless explicitly requested.

Preferred style:

- useVuelidate()

---

## Vue events

Use mitt for lightweight cross-component events.

Rules:

- Use mitt only for simple UI events.
- Do not abuse events for core application state.
- Prefer props and emits for parent-child communication.
- Prefer composables or stores for shared state when needed.

Example event use cases:

- Refresh table after create/update.
- Open or close modal.
- Notify another component of a lightweight UI action.

---

## Activity logging

Do not use Spatie Activity Log.

Use a custom activity logging system.

The activity logger should be reusable and queue-based.

Preferred structure:

- app/Support/Activity/ActivityLogger.php
- app/Jobs/LogActivityJob.php

Activity logging should:

- Be called through a reusable function or service.
- Use a queue job when possible.
- Avoid slowing down the main request.
- Store useful metadata.
- Be simple to call from controllers and services.

Preferred usage style:

activity_log('product.created', [
    'product_id' => $product->id,
    'user_id' => auth()->id(),
]);

Do not scatter direct database inserts for logs across controllers.

---

## Settings

Settings should be centralized and reusable.

If settings become complex, group them by domain:

- general
- seo
- mail
- notifications
- business
- appearance

Avoid hardcoding business settings directly inside views or controllers.

---

## Email templates

If the project needs emails, create an email template customizer in settings.

The email template system should support:

- Subject customization
- Body customization
- Variables/placeholders
- Preview if practical
- Clear fallback defaults

Example placeholders:

- {name}
- {email}
- {phone}
- {order_number}
- {booking_date}
- {company_name}

Email template editing should be admin-friendly and should not require changing code.

Do not hardcode final email content directly inside notification classes if the project requires editable templates.

---

## Database and relationships

Be explicit with database structure.

Use proper foreign keys when possible.

Rules:

- Add indexes for foreign keys and frequently queried columns.
- Use clear relationship methods in models.
- Avoid vague column names.
- Avoid polymorphic relationships unless they are clearly justified.
- Keep migrations readable.

Preferred foreign key style:

$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

---

## API response style

Prefer consistent JSON responses.

For successful actions, use this shape:

{
  "message": "Created successfully.",
  "data": {}
}

For validation errors, rely on Laravel standard validation responses.

For complex resources, use Laravel API Resources.

Do not return inconsistent response shapes from different controllers.

---

## Admin UI style

Use Vue with shadcn-vue style components where suitable.

Admin UI should be:

- Clean.
- Explicit.
- Fast to use.
- Easy to maintain.
- Not over-animated.
- Not overly clever.

Common admin patterns:

- Table/list page.
- Create modal or page.
- Edit modal or page.
- Delete confirmation.
- Filters/search.
- Pagination.
- Toast notifications.

---

## Design and frontend rules

Use Tailwind CSS.

Use DaisyUI only if the project already uses it or if explicitly requested.

Avoid writing large custom CSS unless needed.

Prefer reusable small components.

Do not make huge Vue files. If a file becomes too large, split it into partials.

---

## Multilingual rules

Do not build a full page builder.

For custom Laravel projects, multilingual support should be structured and controlled.

Preferred approach:

- Static UI text uses Laravel lang files.
- Structured CRUD content can use translatable JSON fields when needed.
- Admin forms may use language tabs for fields such as title, description, and content.
- Only support the languages required by the project.

Do not assume every project needs 3 languages.

Do not make clients edit full page layouts unless the project explicitly requires a CMS/page-builder approach.

If the client needs full editable pages, WordPress is usually the better option.

---

## What not to do

Do not:

- Build a custom WordPress replacement.
- Add Filament for complex custom admin workflows.
- Add Livewire for complex interactive apps.
- Use Inertia by default on shared hosting projects.
- Use Spatie Activity Log.
- Create mystery abstractions that hide simple logic.
- Let AI generate large systems without explaining the structure.
- Add packages just because they are popular.

---

## Preferred stack decisions

Use WordPress/WooCommerce for:

- E-commerce.
- Cheap showcase websites.
- Blogs.
- Clients who need page editing.
- Clients who need plugin ecosystem.

Use Laravel + Blade + Vue admin for:

- Premium business websites.
- Catalogs.
- Booking systems.
- Clinic systems.
- E-learning systems.
- Custom dashboards.
- Structured multilingual content.
- Projects with custom business logic.

Use Blade for public SEO pages.

Use Vue for admin/client application interfaces.

Use Laravel API for clear backend logic.

---

## When working on tasks

Before editing, identify:

1. What feature/module is involved.
2. Which files are likely needed.
3. Whether the change affects backend, Vue admin, Blade frontend, or database.
4. The smallest safe patch.

Then make the change.

Do not expand the scope unless required.
