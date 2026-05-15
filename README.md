<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel Business Kit

This is a Laravel Business Kit implementation following the specifications in PLAN.md. It provides a premium structured business website system with:

### ✅ Core Features Implemented

- **Authentication**: Laravel Sanctum-based API authentication
- **Users**: Complete CRUD with role/permission management
- **Roles & Permissions**: Using spatie/laravel-permission with seeders
- **Custom Activity Log**: Queue-based system (replaced Spatie Activitylog per PLAN.md)
- **Settings**: Centralized system with groups (general, seo, mail, notifications, business, appearance, social, integrations)
- **Email Templates**: Customizable template system with variables
- **Services CRUD**: Full create, read, update, delete operations
- **Projects CRUD**: Full create, read, update, delete operations
- **Testimonials CRUD**: Full create, read, update, delete operations
- **FAQ CRUD**: Full create, read, update, delete operations
- **Contact Messages**: Full management system
- **SEO Fields**: Per item/page SEO fields (title, description, keywords)
- **Media Uploads**: Integrated with avatar/system upload handling
- **API Resources**: Consistent JSON responses using Laravel API Resources

### 📋 Implementation Details

#### Activity Log System (Custom)
- Queue-based to not slow down requests
- Stores useful metadata (user_id, event, subject, properties, IP, user_agent)
- Simple usage: `activity_log('event.name', [context])`

#### Settings System
- Groups: general, seo, mail, notifications, business, appearance, social, integrations
- Type safety (string, integer, boolean, json, array)
- Public/private setting distinction
- Caching ready

#### Email Templates
- Subject and body customization
- Variable placeholder support ({name}, {email}, etc.)
- Preview functionality
- Active/inactive toggling

#### Business Modules (Services, Projects, Testimonials, FAQs)
- Full CRUD operations
- SEO fields per item
- Media upload handling
- Soft deletes
- Ordering capabilities
- API resources for consistent responses
- Activity logging on create/update/delete

#### Contact Messages
- Read/unread status
- Reply tracking
- Timestamping
- Filtering capabilities

### 🔧 Technical Implementation

#### Backend Conventions Followed
- Controllers for API endpoints
- FormRequest-style validation (in controllers for simplicity)
- API Resources for response shaping
- Services for reusable logic (MediaUploadService planned)
- Jobs for background processing (Activity logging)
- Proper model relationships and scopes
- Database indexes for performance
- Soft deletes where appropriate
- Proper foreign key constraints

#### API Response Format
Consistent JSON responses:
```json
{
  "data": [
    {
      "id": 1,
      "title": "Service Name",
      // ... other fields
    }
  ]
}
```

Single item responses:
```json
{
  "data": {
    // item data
  }
}
```

### 🚀 Getting Started

1. Install dependencies:
   ```bash
   composer install
   npm install
   ```

2. Copy environment file:
   ```bash
   cp .env.example .env
   ```

3. Generate application key:
   ```bash
   php artisan key:generate
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Seed permissions:
   ```bash
   php artisan db:seed --class=PermissionSeeder
   ```

6. Start development servers:
   ```bash
   php artisan serve
   npm run dev
   ```

### 📚 API Documentation

#### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `GET /api/user` - Get current user (authenticated)
- `POST /api/logout` - Logout user

#### Users
- `GET /api/users` - List users
- `POST /api/users` - Create user
- `GET /api/users/{user}` - Get user
- `PUT /api/users/{user}` - Update user
- `DELETE /api/users/{user}` - Delete user
- `POST /api/users/invite` - Invite user
- `POST /api/users/{user}/assign-role` - Assign role
- `POST /api/users/{user}/give-permission` - Give permission
- `POST /api/users/{user}/revoke-permission` - Revoke permission
- `POST /api/users/{user}/avatar` - Upload avatar
- `DELETE /api/users/{user}/avatar` - Delete avatar

#### Roles & Permissions
- `GET /api/roles` - List roles
- `POST /api/roles` - Create role
- `GET /api/roles/{role}` - Get role
- `PUT /api/roles/{role}` - Update role
- `DELETE /api/roles/{role}` - Delete role
- `POST /api/roles/{role}/assign-permissions` - Assign permissions

- `GET /api/permissions` - List permissions
- `POST /api/permissions` - Create permission
- `GET /api/permissions/{permission}` - Get permission
- `PUT /api/permissions/{permission}` - Update permission
- `DELETE /api/permissions/{permission}` - Delete permission

#### Activity Log
- `GET /api/activity-logs` - List activity logs
- `GET /api/activity-logs/{activity}` - Get activity log
- `GET /api/activity-logs/log-names` - Get log names
- `GET /api/activity-logs/events` - Get events

#### Settings
- `GET /api/settings` - List settings
- `POST /api/settings` - Create setting
- `GET /api/settings/{setting}` - Get setting
- `PUT /api/settings/{setting}` - Update setting
- `DELETE /api/settings/{setting}` - Delete setting
- `GET /api/settings/value/{key}` - Get setting value (public)

#### Email Templates
- `GET /api/email-templates` - List templates
- `POST /api/email-templates` - Create template
- `GET /api/email-templates/{template}` - Get template
- `PUT /api/email-templates/{template}` - Update template
- `DELETE /api/email-templates/{template}` - Delete template
- `POST /api/email-templates/{template}/preview` - Preview template

#### Business Modules
- `GET /api/services` - List services
- `POST /api/services` - Create service
- `GET /api/services/{service}` - Get service
- `PUT /api/services/{service}` - Update service
- `DELETE /api/services/{service}` - Delete service

(Same pattern for projects, testimonials, faqs, contact-messages)

### 📁 Folder Structure

```
app/
  Http/
    Controllers/
      Api/                  # All API controllers
    Resources/              # API resource classes
  Models/                   # Eloquent models
  Support/
    Activity/               # Custom activity log system
  Jobs/                     # Queue jobs
  Services/                 # Business logic services
database/
  migrations/               # Database migrations
  seeders/                  # Database seeders
routes/
  api.php                   # API routes
resources/
  js/
    admin/                  # Vue admin views (to be implemented)
  views/
    layouts/
      public.blade.php      # Public Blade layout
    pages/                  # Public Blade pages
```

### 🔜 Next Steps / Optional Features

Per PLAN.md, these can be added as needed:

1. **Multilingual Support**
   - Static language files (lang/fr/, lang/ar/, lang/en/)
   - JSON translatable fields for structured content
   - Language switcher and Vue language tabs

2. **Optional Modules**
   - Catalog system
   - Booking-lite system
   - Blog-lite system
   - Team members
   - Partners

3. **Advanced Features**
   - Custom dashboard widgets
   - Advanced analytics/reporting
   - Notification system
   - File manager
   - Backup system

### 📝 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### 💡 Design Philosophy

This implementation follows the Laravel Business Kit specifications from PLAN.md:
- Explicit, maintainable code over magic-heavy abstractions
- Laravel handles backend logic, APIs, validation, auth, queues, permissions
- Vue handles admin/client application interfaces
- Blade is used for public SEO pages and landing pages
- Avoids Filament, Livewire, Inertia, and Spatie Activity Log per guidelines
- Prefers boring, clear code over clever code