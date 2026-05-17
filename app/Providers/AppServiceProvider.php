<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\Media;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use App\Policies\ContactMessagePolicy;
use App\Policies\EmailTemplatePolicy;
use App\Policies\FaqPolicy;
use App\Policies\MediaPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServicePolicy;
use App\Policies\SettingPolicy;
use App\Policies\TestimonialPolicy;
use App\Policies\UserPolicy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Testimonial::class, TestimonialPolicy::class);
        Gate::policy(Faq::class, FaqPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);
        Gate::policy(EmailTemplate::class, EmailTemplatePolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
    }
}
