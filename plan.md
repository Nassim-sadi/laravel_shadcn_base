# NsBase Project Plan

## Progress - Updated Apr 20, 2026

### Completed

- [x] Create Laravel 13 project (NsBase)
- [x] Install Laravel Sanctum
- [x] Set up User model with roles (super_admin, admin, user)
- [x] Install Spatie Permission & Activitylog
- [x] Create migrations (users, permissions, activity_log)
- [x] Create AuthController API endpoints
- [x] Clone Shadcn-Vue admin frontend
- [x] Install admin dependencies
- [x] Configure frontend to connect to Laravel API
- [x] Create auth API service
- [x] Update use-auth composable
- [x] Update login form to call API
- [x] Update sign-up page to call API
- [x] Fix build errors (Zod v3 API, routes, layouts)
- [x] Set up Vue Router with nested routes
- [x] Add sidebar layout to admin pages
- [x] Create /adminDashboard route with sidebar
- [x] Clean up sidebar navigation

### In Progress

- [x] Test authentication flow
- [ ] Set up user management pages
- [ ] Set up roles/permissions management
- [ ] Set up activity log viewer
- [ ] Set up profile/settings pages
- [ ] Fix any remaining issues

### Pending

- [ ] Copy over NsClinic2 API logic
- [ ] Test full integration
- [ ] Production ready

## Routes

- `/` - Landing page (public)
- `/auth/sign-in` - Sign in page
- `/auth/sign-up` - Sign up page
- `/admin` - Redirects to /adminDashboard
- `/adminDashboard` - Dashboard with sidebar
- `/adminDashboard/users` - Users management
- `/adminDashboard/roles` - Roles management
- `/adminDashboard/permissions` - Permissions management
- `/adminDashboard/settings` - Settings pages

## Tech Stack

- Laravel 13 (API)
- Vue 3 + TypeScript (Frontend)
- Vite (Build tool)
- Tailwind CSS + shadcn-vue (UI)
- Pinia (State)
- Vue Router (Routing)
- TanStack Vue Query (Data fetching)
- Spatie (Permissions/Activitylog)

## GitHub

https://github.com/Nassim-sadi/laravel_shadcn_base