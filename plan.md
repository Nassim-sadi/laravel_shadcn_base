# NsBase Project Plan

## Progress - Updated Apr 21, 2026

### Backend (Laravel)

- [x] Create Laravel 13 project (NsBase)
- [x] Install Laravel Sanctum
- [x] Set up User model with roles (super_admin, admin, user)
- [x] Install Spatie Permission & Activitylog
- [x] Create migrations (users, permissions, activity_log)
- [x] Fix Sanctum recursive guard loop - memory exhaustion fix (guard => 'web')
- [x] Create AuthController API endpoints
- [x] API returns roles and permissions with user data
- [x] PermissionSeeder creates default permissions and roles
- [x] User model uses HasRoles trait from Spatie

### Frontend (Vue)

- [x] Clone Shadcn-Vue admin frontend
- [x] Install admin dependencies
- [x] Configure frontend to connect to Laravel API
- [x] Create auth API service
- [x] Update use-auth composable
- [x] Fix token not being sent - added Authorization header
- [x] Create use-role.ts composable with hasRole, hasPermission helpers
- [x] Create v-permission directive for hiding elements
- [x] Update auth guard to check roles and permissions
- [x] Fix users page - data path was wrong (response.data vs response.data.data)
- [x] Add JS logging to debug users loading issue
- [x] Rebuild settings page with profile form, avatar upload, password change
- [x] Create image-utils.ts with compressImage and formatFileSize functions
- [x] Clean up sidebar navigation

### Routes

- `/` - Landing page (public)
- `/auth/sign-in` - Sign in page
- `/auth/sign-up` - Sign up page
- `/admin` - Redirects to /adminDashboard
- `/adminDashboard` - Dashboard with sidebar
- `/adminDashboard/users` - Users management
- `/adminDashboard/roles` - Roles management
- `/adminDashboard/permissions` - Permissions management
- `/adminDashboard/settings` - Settings pages

### In Progress

- [x] Test user loading (5s delay debug with JS logging)
- [x] Implement client-side image compression with progress
- [x] Add progress indicator for avatar upload
- [x] Add UserResource & UserCollection for consistent API responses

### Pending

- [ ] Copy over NsClinic2 API logic
- [x] Test full integration (needs testing)
- [ ] Production ready

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