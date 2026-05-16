# PLAN.md

## Goal

Build a clear, repeatable business development system.

The goal is not to create one giant CMS that does everything.

The goal is to have clear product lines:

1. WordPress / WooCommerce for ecosystem-heavy projects.
2. Laravel Business Kit for premium structured business websites.
3. Laravel App Kit for serious custom platforms like e-learning and clinic systems.

The stack must match what is easy to build, debug, deploy, and maintain without depending too much on expensive AI usage.

---

## Core decision

Use the stack based on the project type.

### WordPress / WooCommerce

Use for:

- E-commerce.
- Blogs.
- Cheap showcase websites.
- Clients who need to edit pages freely.
- Clients who need many plugins.
- Clients who need WooCommerce, delivery plugins, payment plugins, SEO plugins, or Adsense plugins.

This is the default for normal low-budget client websites.

### Laravel Business Kit

Use for:

- Premium business websites.
- Structured content websites.
- Catalog websites.
- Booking-lite websites.
- Multilingual structured content.
- Clients who do not need full page editing.
- Clients who need custom logic but not a full app.

This is not a WordPress replacement.

This is a controlled structured-content system.

### Laravel App Kit

Use for:

- E-learning platforms.
- Clinic systems.
- Dashboards.
- Client portals.
- Advanced booking systems.
- Systems with roles, workflows, permissions, progress, reporting, or business logic.

This is app-first, not CMS-first.

---

## Stack rules

### Public frontend

Use Blade for:

- SEO pages.
- Landing pages.
- Home page.
- Service pages.
- Project pages.
- Public catalog pages.
- Public business pages.

Reason:

- Better for shared hosting.
- Faster first load.
- Good SEO.
- Easy deployment.
- Less JavaScript required.

### Admin and app frontend

Use Vue for:

- Admin dashboard.
- Client dashboard.
- Student dashboard.
- Clinic dashboard.
- Booking management.
- Catalog management.
- Any complex interactive UI.

Reason:

- Clear logic.
- Easier to debug.
- Easier for AI to help without creating magic.
- Matches preferred workflow.

### Backend

Use Laravel for:

- API.
- Auth.
- Validation.
- Permissions.
- Queues.
- Jobs.
- Services.
- Models.
- Migrations.
- Notifications.
- Email templates.
- Activity logging.

### Avoid by default

Do not use by default:

- Filament for complex custom workflows.
- Livewire for complex apps.
- Inertia on shared hosting.
- Custom page builders.
- Spatie Activity Log.

Use Filament only for simple CRUD if it is already understood and clearly saves time.

---

## Product line 1: WordPress / WooCommerce

### Use cases

- E-commerce.
- Blogs.
- Cheap business websites.
- Clients who want to add and edit pages.
- Clients who want plugin ecosystem.
- Clients who need Adsense quickly.
- Clients who need WooCommerce ecosystem in Algeria.

### Default features

- WordPress.
- WooCommerce when needed.
- SEO plugin.
- Cache plugin.
- Contact form.
- WhatsApp button.
- Simple analytics.
- Delivery integration when required.
- COD-friendly checkout for Algerian e-commerce.
- Optional GTranslate or Polylang depending on budget.

### Language policy

Default: one language.

Optional multilingual:

- Low budget: GTranslate.
- Medium budget: Polylang if the site structure is simple.
- Avoid complex multilingual Elementor/Polylang setups unless the budget justifies it.

### When not to use WordPress

Do not use WordPress when:

- The project needs custom workflows.
- The project needs advanced roles.
- The project needs complex dashboards.
- The project needs heavy custom logic.
- The admin UX needs to be custom and controlled.
- Plugin dependency becomes risky.

---

## Product line 2: Laravel Business Kit

### Purpose

A premium structured business-site blueprint.

This is for clients who need a custom Laravel website with controlled editable content, not full page editing.

The client edits content records, not layouts.

### Stack

- Laravel.
- Blade public frontend.
- Vue admin.
- Laravel API.
- Tailwind CSS.
- shadcn-vue style admin components.
- Vuelidate for Vue validation.
- mitt for lightweight Vue events.
- Custom queue-based activity logging.
- Custom settings system.
- Custom email template system.

### Core modules

Must include:

- Auth.
- Users.
- Roles and permissions.
- Settings.
- Activity log.
- Media uploads.
- Services CRUD.
- Projects CRUD.
- Testimonials CRUD.
- FAQ CRUD.
- Contact messages.
- SEO fields.
- Email templates.

Optional modules:

- Catalog.
- Booking-lite.
- Blog-lite.
- Team members.
- Partners.
- Landing pages with controlled sections.

### Public pages

Default public pages:

- Home.
- About.
- Services.
- Service details.
- Projects.
- Project details.
- Contact.
- FAQ.
- Optional catalog.
- Optional booking page.

### Admin pages

Default admin pages:

- Dashboard.
- Users.
- Roles.
- Settings.
- Services.
- Projects.
- Testimonials.
- FAQ.
- Contact messages.
- Media.
- Activity logs.
- Email templates.

### What clients can edit

Clients can edit:

- Service titles and descriptions.
- Project titles and descriptions.
- Images.
- Testimonials.
- FAQs.
- Contact info.
- Social links.
- SEO titles and descriptions.
- Translation values for structured content when multilingual is enabled.
- Email template subject/body when needed.

Clients cannot edit:

- Page layouts.
- Random new page structures.
- Section order unless explicitly built.
- Theme architecture.
- Blade templates.
- Core logic.

If the client needs full page editing, use WordPress instead.

---

## Laravel Business Kit multilingual policy

Do not assume every project needs 3 languages.

Use levels.

### Level 0: Single language

Default.

Use one main language:

- French.
- Arabic.
- English.

This should be the default for most clients.

### Level 1: Static UI translations

Use Laravel lang files for:

- Buttons.
- Navigation.
- Labels.
- Validation messages.
- Fixed UI text.
- Reusable public text.

Example folders:

- lang/fr/
- lang/ar/
- lang/en/

### Level 2: Translatable structured content

Use JSON fields for structured CRUD content only.

Examples:

- service title.
- service short description.
- service description.
- project title.
- project description.
- FAQ question.
- FAQ answer.

Admin forms should use language tabs only for enabled languages.

Do not enable FR/AR/EN automatically for every project.

### Level 3: Full editable pages

Do not build this in Laravel Business Kit.

If the client needs full page editing, use WordPress.

---

## Product line 3: Laravel App Kit

### Purpose

A serious app blueprint for custom platforms.

Use this for e-learning, clinic systems, advanced dashboards, portals, and workflows.

### Stack

- Laravel API.
- Vue admin/client dashboards.
- Blade landing page.
- Tailwind CSS.
- shadcn-vue style components.
- FormRequest validation.
- Vuelidate frontend validation.
- Sanctum if API authentication is needed.
- Roles and permissions.
- Queues and jobs.
- Notifications.
- Activity log.
- Email templates.

### App Kit core modules

Must include:

- Auth.
- Users.
- Roles and permissions.
- Settings.
- Activity log.
- Notifications.
- Email templates.
- File/media uploads.
- Dashboard layout.
- API response conventions.
- Error handling.
- Audit-friendly logs.

---

## E-learning platform blueprint

### Purpose

A custom e-learning system.

### Frontend structure

- Blade landing page.
- Vue admin dashboard.
- Vue student dashboard.

### Roles

- Admin.
- Instructor.
- Student.

Optional:

- Parent.
- Moderator.

### Core modules

- Courses.
- Course categories.
- Lessons.
- Lesson content.
- Enrollments.
- Student progress.
- Quizzes, optional.
- Certificates, optional.
- Payments, optional.
- Announcements.
- Notifications.
- Email templates.

### Build phases

Phase 1:

- Auth.
- Users and roles.
- Course CRUD.
- Lesson CRUD.
- Student enrollment.
- Student course view.
- Progress tracking.

Phase 2:

- Quizzes.
- Attachments.
- Certificates.
- Notifications.
- Admin reports.

Phase 3:

- Payments.
- Advanced analytics.
- Instructor dashboard.
- Comments/discussions.

---

## Clinic platform blueprint

### Purpose

A custom clinic/medical appointment system.

### Frontend structure

- Blade landing page.
- Vue admin dashboard.
- Vue doctor/reception dashboard if needed.
- Vue patient dashboard only if needed.

### Roles

- Admin.
- Doctor.
- Receptionist.
- Patient.

### Core modules

- Doctors.
- Services.
- Patients.
- Appointments.
- Schedules.
- Availability.
- Notifications.
- Contact messages.
- Activity log.
- Email/SMS templates if needed.

### Build phases

Phase 1:

- Auth.
- Users and roles.
- Doctor CRUD.
- Service CRUD.
- Appointment CRUD.
- Schedule/availability basics.
- Public booking request.

Phase 2:

- Doctor dashboard.
- Receptionist workflow.
- Appointment status flow.
- Notifications.

Phase 3:

- Patient dashboard.
- Medical notes, only if legally and operationally justified.
- Reports.
- Advanced reminders.

---

## Catalog blueprint

### Purpose

A structured product/service catalog without full e-commerce checkout.

Use this when the client wants products displayed but does not need WooCommerce.

### Stack

- Blade public catalog.
- Vue admin.
- Laravel API.

### Core modules

- Categories.
- Products/items.
- Product images.
- Attributes, optional.
- Brands, optional.
- Tags, optional.
- Contact/quote request.
- SEO fields.

### Public pages

- Catalog index.
- Category page.
- Product details page.
- Search/filter page if needed.

### Admin pages

- Products table.
- Create product.
- Edit product.
- Categories.
- Images.
- Attributes if required.

### Database basics

Suggested tables:

- categories.
- products.
- product_images.
- product_attributes, optional.
- brands, optional.
- quote_requests, optional.

### Important rule

Build catalog admin with Vue + API, not mystery Filament CRUD.

---

## Booking blueprint

### Purpose

A clear booking system that avoids Livewire/Filament magic.

### Stack

- Blade public booking page or Vue island.
- Vue admin.
- Laravel API.

### Core modules

- Services.
- Staff/resources, optional.
- Availability.
- Time slots.
- Bookings.
- Booking statuses.
- Notifications.
- Email templates.

### Booking statuses

Default statuses:

- pending.
- confirmed.
- cancelled.
- completed.
- no_show.

### Build phases

Phase 1:

- Service CRUD.
- Availability basics.
- Public booking request.
- Admin booking list.
- Status update.

Phase 2:

- Time slot rules.
- Staff/resource assignment.
- Email notifications.
- Calendar view.

Phase 3:

- Client dashboard.
- Reminders.
- Advanced capacity rules.

---

## Backend conventions

### Controllers

Use controllers for API endpoints.

Suggested folders:

- app/Http/Controllers/Api/Admin/
- app/Http/Controllers/Api/Public/

### Requests

Use FormRequest classes for validation.

Suggested folders:

- app/Http/Requests/Admin/
- app/Http/Requests/Public/

### Resources

Use API Resources when shaping output matters.

Suggested folder:

- app/Http/Resources/

### Services and actions

Use services/actions only when logic is reusable or complex.

Do not create unnecessary abstractions.

Suggested folders:

- app/Services/
- app/Actions/

### Jobs

Use jobs for slow or background tasks.

Suggested folder:

- app/Jobs/

Use jobs for:

- Activity logging.
- Email sending.
- Imports.
- Exports.
- Heavy image processing.
- Background notifications.

---

## Frontend conventions

### Admin Vue structure

Each module gets its own folder.

Example:

- resources/js/admin/views/users/Index.vue
- resources/js/admin/views/users/partials/Create.vue
- resources/js/admin/views/users/partials/Edit.vue
- resources/js/admin/views/users/partials/Form.vue

The Index.vue file holds the table/list.

The partials folder holds create, edit, and form partials.

### Shared admin components

Use:

- resources/js/admin/components/

Examples:

- DataTable.vue.
- ConfirmDialog.vue.
- FormInput.vue.
- ImageUploader.vue.
- Pagination.vue.
- PageHeader.vue.
- EmptyState.vue.

Do not put feature-specific components in the shared folder.

### Public Vue/client app structure

Use:

- resources/js/views/

Example:

- resources/js/views/Index.vue.

For larger client-side views:

- resources/js/views/booking/Index.vue.
- resources/js/views/booking/partials/Calendar.vue.
- resources/js/views/booking/partials/TimeSlots.vue.

### Blade public structure

Use:

- resources/views/layouts/public.blade.php.
- resources/views/pages/home.blade.php.
- resources/views/pages/services.blade.php.
- resources/views/pages/projects.blade.php.
- resources/views/pages/contact.blade.php.

---

## Auth and permissions

Use a clear role/permission system.

Recommended roles depend on product type.

### Business Kit roles

- Owner.
- Admin.
- Editor.

### E-learning roles

- Admin.
- Instructor.
- Student.

### Clinic roles

- Admin.
- Doctor.
- Receptionist.
- Patient.

### Rules

- Keep permissions explicit.
- Do not hide permission logic in random places.
- Use policies or clear checks.
- Do not overcomplicate permissions before needed.

---

## Activity log plan

Do not use Spatie Activity Log.

Build a custom reusable queue-based system.

### Goals

- Simple to call.
- Does not slow requests.
- Stores useful metadata.
- Works across modules.

### Suggested structure

- app/Support/Activity/ActivityLogger.php.
- app/Jobs/LogActivityJob.php.
- app/Models/ActivityLog.php.
- database/migrations/create_activity_logs_table.php.

### Suggested fields

- id.
- user_id.
- event.
- subject_type.
- subject_id.
- description.
- properties JSON.
- ip_address.
- user_agent.
- created_at.

### Usage style

Call a helper/service like:

activity_log('product.created', [
    'product_id' => $product->id,
    'user_id' => auth()->id(),
]);

The helper dispatches a queue job.

---

## Settings plan

Create a centralized settings system.

### Groups

- general.
- seo.
- mail.
- notifications.
- business.
- appearance.
- social.
- integrations.

### Rules

- Do not hardcode business settings in views.
- Cache settings when useful.
- Keep fallback defaults.
- Make settings editable from admin if clients need them.

---

## Email template plan

Create an email template customizer when emails are part of the project.

### Template fields

- key.
- name.
- subject.
- body.
- variables.
- is_active.

### Required features

- Subject customization.
- Body customization.
- Placeholder variables.
- Preview if practical.
- Fallback default content.

### Example variables

- {name}
- {email}
- {phone}
- {order_number}
- {booking_date}
- {company_name}
- {course_name}
- {appointment_date}

### Rule

Do not hardcode final email content inside notification classes if editable templates are required.

---

## Media upload plan

Use a clear media upload strategy.

### Needed features

- Upload image.
- Delete image.
- Replace image.
- Store alt text when SEO matters.
- Associate media with modules.
- Validate file type and size.

### Avoid

- Random file paths scattered across controllers.
- Upload logic duplicated in every module.

---

## SEO plan

For Laravel public pages, include:

- Title.
- Meta description.
- Canonical URL.
- Open Graph title.
- Open Graph description.
- Open Graph image.
- Sitemap.
- robots.txt.
- Clean slugs.
- Fast loading pages.
- Schema only when useful.

### SEO fields for structured content

For services/projects/products:

- slug.
- meta_title.
- meta_description.
- og_image.
- is_indexable.

### Rule

Do not over-engineer SEO before content quality is good.

---

## Deployment plan

Target shared hosting compatibility when possible.

### Laravel deployment basics

- composer install --no-dev --optimize-autoloader.
- npm run build.
- php artisan config:cache.
- php artisan route:cache.
- php artisan view:cache.
- php artisan migrate --force.
- php artisan queue:restart.
- Ensure correct PHP version.
- Ensure storage link exists.
- Ensure .env is correct.

### Shared hosting warning

Avoid stacks that make first load heavy:

- Inertia by default.
- Full SPA for public SEO pages.
- Huge JS bundles.
- Unnecessary frontend libraries.

---

## AI usage plan

AI should help with narrow tasks, not own the project.

### Good AI tasks

- Create FormRequest.
- Create migration.
- Review controller.
- Generate Vue form.
- Fix a specific bug.
- Refactor one module.
- Explain one file.
- Write tests for one feature.
- Generate copy or labels.

### Bad AI tasks

- Build the entire platform.
- Refactor the whole project.
- Decide architecture without constraints.
- Generate huge magical systems.
- Touch unrelated files.
- Scan the whole repo.

### Rule

Always scope AI prompts to specific files and modules.

---

## Build order for the new Laravel blueprint

### Phase 0: Freeze decisions

Decide:

- Laravel version.
- Vue setup.
- Tailwind/shadcn-vue setup.
- Auth approach.
- Permission approach.
- API response style.
- Folder conventions.
- Deployment target.

### Phase 1: Core foundation

Build:

- Auth.
- Users.
- Roles.
- Permissions.
- Admin layout.
- API structure.
- FormRequest conventions.
- Shared Vue admin layout.
- Shared admin components.
- Toast notifications.
- Confirm dialog.
- Pagination pattern.
- Error handling pattern.

### Phase 2: System modules

Build:

- Settings.
- Activity log.
- Media uploads.
- Email templates.
- Contact messages.

### Phase 3: Business content modules

Build:

- Services CRUD.
- Projects CRUD.
- Testimonials CRUD.
- FAQ CRUD.
- SEO fields.
- Public Blade pages for services/projects.

### Phase 4: Multilingual support

Build only if needed:

- Static lang files.
- Enabled languages setting.
- Language switcher.
- JSON translatable fields for services/projects/FAQ.
- Vue language tabs.

Do not build full page editing.

### Phase 5: Optional modules

Build only when needed:

- Catalog.
- Booking-lite.
- Blog-lite.
- Team members.
- Partners.

### Phase 6: Product-specific kits

Build separate branches/templates for:

- E-learning.
- Clinic.
- Catalog.
- Booking.

Do not merge every possible module into one bloated template.

---

## What not to build

Do not build:

- A custom WordPress replacement.
- A full page builder.
- A universal CMS.
- A giant template with every module enabled.
- Complex Filament admin workflows.
- Complex Livewire apps.
- Three-language support by default for every client.
- A blog engine unless the project needs it.
- E-commerce in Laravel for normal Algerian clients when WooCommerce is better.

---

## Pricing and positioning notes

### WordPress line

Position as:

- Fast.
- Affordable.
- Editable.
- Plugin ecosystem.
- Good for e-commerce/blogs.

### Laravel Business Kit

Position as:

- Custom.
- Structured.
- Faster and cleaner than plugin-heavy sites.
- Better for specific business logic.
- Better admin experience when page editing is not needed.
- More expensive than WordPress.

### Laravel App Kit

Position as:

- Full custom platform.
- Built around the client's workflow.
- Roles and dashboards.
- Serious system, not a simple website.
- Highest price tier.

---

## Final decision rules

Use WordPress if:

- The client needs page editing.
- The client needs blog publishing.
- The client needs WooCommerce.
- The budget is low.
- The plugin ecosystem solves the problem.

Use Laravel Business Kit if:

- The client needs a premium custom business website.
- The content is structured.
- The client does not need free page editing.
- The client needs multilingual structured content.
- The client needs catalog or booking-lite.

Use Laravel App Kit if:

- The project is really an application.
- The project has roles, workflows, dashboards, progress, appointments, or business logic.
- The frontend is mostly Vue.
- The landing/public pages are Blade.

Use Filament only if:

- The CRUD is boring.
- The screen is simple.
- The implementation is understood.
- It saves time without creating mystery.

If the system becomes mysterious, stop and rebuild that part using Vue + API.

---

## Current priority

Do not rewrite nassimstudio.com immediately.

Current priority:

1. Keep the existing site working.
2. Use it as proof/portfolio if it sells.
3. Build the new Laravel Business Kit separately.
4. Move future custom work to the clearer Blade + Vue admin + Laravel API structure.
5. Keep WordPress/WooCommerce as the default for e-commerce and normal blogs.

The goal is to become faster, clearer, and less dependent on expensive AI.

---

## Current build progress (NsBase — Laravel Business Kit scaffold)

### What is built

| Area | Status |
|------|--------|
| Auth (login, register, profile, password change, avatar upload with compression) | Done |
| Users CRUD + invite + assign roles | Done |
| Roles & permissions CRUD (Spatie) | Done |
| Admin layout (sidebar width 18rem, theme, sticky header) | Done |
| Activity log (custom queue-based) | Done |
| Settings CRUD (general, SEO, mail, etc.) | Done |
| Email templates CRUD + render() with placeholders + `TemplateMailable` | Done |
| Contact messages (API CRUD, admin list, `ContactMessageNotification`) | Done |
| Services CRUD (multilingual JSON fields, Sheet sidebar) | Done |
| Projects CRUD (multilingual JSON fields, Sheet sidebar) | Done |
| Testimonials CRUD (multilingual JSON fields, Sheet sidebar) | Done |
| FAQs CRUD (multilingual JSON fields, Sheet sidebar) | Done |
| Admin CRUD forms: Sheet sidebar (`xl:max-w-2xl`) + ConfirmDialog delete pattern + 2-column grid | Done for 5 modules |
| Multilingual: `HasTranslatedAttributes` trait, lang JSON files, Vue language tabs, dynamic locale loading | Done |
| Public Blade: Home, Services index/show, Projects index/show | Done |
| About Blade page (static, content via `__('about.*')` lang files) | Done |
| Contact Blade page (form + throttled store, rate-limit 3/10min) | Done |
| Public nav updated (About, Contact, Services, Projects links) | Done |
| Media manager: migration, model, Admin API CRUD + bulkDestroy + thumbnail (intervention/image v4) | Done |
| Media Vue admin: grid, custom modal (MediaLibrary + Upload tabs, inline edit, selectMode) | Done — refactored from Sheet to custom modal |
| `ImagePickerField.vue` reusable component + `MediaModal.vue` custom modal | Done — integrated into services, projects, testimonials forms |
| `useMediaUpload` composable (multi-file drag-drop XHR upload with per-file progress) | Done |
| `image_id` FK added to services, projects, testimonials + backend eager loading | Done |
| Form validation: Vuelidate error messages under every field, `translatedRequired()` helper | Done |
| Unsaved changes guard: simplified always-show confirm dialog | Done — smart composable removed |
| `DialogContent` accessibility: hidden `DialogDescription` in Sheet/MediaPicker/ContactMessages | Done |
| `Switch` import fix in testimonials page | Done |
| CSS: `.admin-form-field` class for consistent label-input spacing | Done |
| `ContactMessageNotification` (ShouldQueue, uses `TemplateMailable`) | Done |
| Contact form sends confirmation + admin notification | Done |
| PHP feature tests for API and localization | Done |
| DEPLOY.md — one-command deploy flow with shared hosting notes | Done |
| `npm run build` passes with 0 TypeScript errors | Done |

### Not built (ready when needed)

- Catalog module
- Booking-lite module
- Blog-lite module
- Team members CRUD
- Partners CRUD
- Landing pages with controlled sections
- Galleries pivot table (`mediaables`)
- Email sending integration (Mailhog/SMTP config) — notification classes exist, need SMTP setup
- Sitemap / robots.txt / canonical URLs / OG tags pass — basic OG exists, can be enhanced
- Content seeders (ContentSeeder exists but may need fine-tuning)

### Next steps

1. **Email sending** — configure SMTP/Mailhog, verify `ContactMessageNotification` sends correctly via queue worker (`php artisan queue:work`).
2. **Seo pass** — add sitemap.xml, robots.txt, canonical URLs, richer OG tags to public Blade layouts.
3. **Seeders** — verify and refine `ContentSeeder` for realistic test data (services, projects, testimonials, faqs, settings).
4. **Production deploy** — follow DEPLOY.md to deploy to staging/production server.
5. **Optional modules** — catalog, booking-lite, blog-lite, team, partners when client needs them.

After these, the Business Kit scaffold is ready for the first real client project.
