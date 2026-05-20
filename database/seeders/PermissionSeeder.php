<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
            'roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
            'permissions' => ['permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'],
            'settings' => ['settings.view', 'settings.edit'],
            'ai' => ['ai.generate', 'ai.import'],
            'logs' => ['logs.view'],
            'media' => ['media.view', 'media.create', 'media.edit', 'media.delete'],
            'services' => ['services.view', 'services.create', 'services.edit', 'services.delete'],
            'projects' => ['projects.view', 'projects.create', 'projects.edit', 'projects.delete'],
            'testimonials' => ['testimonials.view', 'testimonials.create', 'testimonials.edit', 'testimonials.delete'],
            'faqs' => ['faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete'],
            'contact-messages' => ['contact-messages.view', 'contact-messages.create', 'contact-messages.edit', 'contact-messages.delete'],
            'email-templates' => ['email-templates.view', 'email-templates.create', 'email-templates.edit', 'email-templates.delete'],
            'blog' => ['blogs.view', 'blogs.create', 'blogs.edit', 'blogs.delete'],
            'catalog' => ['catalog.view', 'catalog.create', 'catalog.edit', 'catalog.delete'],
        ];

        $modules = config('modules', []);
        $standardModules = ['services', 'projects', 'testimonials', 'faqs', 'media'];
        foreach ($modules as $module => $enabled) {
            if ($enabled && in_array($module, $standardModules, true)) {
                $groups[$module] = [
                    "{$module}.view",
                    "{$module}.create",
                    "{$module}.edit",
                    "{$module}.delete",
                ];
            }
        }

        foreach ($groups as $group => $perms) {
            foreach ($perms as $name) {
                Permission::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'web',
                ]);
            }
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $admin->syncPermissions([
            'users.view', 'users.create', 'users.edit',
            'roles.view', 'roles.create', 'roles.edit',
            'permissions.view',
            'settings.view', 'settings.edit',
            'ai.generate', 'ai.import',
            'logs.view',
            'media.view', 'media.create', 'media.edit',
            'services.view', 'services.create', 'services.edit',
            'projects.view', 'projects.create', 'projects.edit',
            'testimonials.view', 'testimonials.create', 'testimonials.edit',
            'faqs.view', 'faqs.create', 'faqs.edit',
            'contact-messages.view', 'contact-messages.create', 'contact-messages.edit',
            'email-templates.view', 'email-templates.create', 'email-templates.edit',
            'blogs.view', 'blogs.create', 'blogs.edit',
            'catalog.view', 'catalog.create', 'catalog.edit',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);
        $user->syncPermissions([
            'settings.view',
        ]);
    }
}
