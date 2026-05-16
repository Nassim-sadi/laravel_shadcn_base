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
            'permissions' => ['permissions.view', 'permissions.manage'],
            'settings' => ['settings.view', 'settings.edit'],
            'logs' => ['logs.view'],
            'services' => ['services.view', 'services.create', 'services.edit', 'services.delete'],
            'projects' => ['projects.view', 'projects.create', 'projects.edit', 'projects.delete'],
            'testimonials' => ['testimonials.view', 'testimonials.create', 'testimonials.edit', 'testimonials.delete'],
            'faqs' => ['faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete'],
            'contact-messages' => ['contact-messages.view', 'contact-messages.create', 'contact-messages.edit', 'contact-messages.delete'],
            'email-templates' => ['email-templates.view', 'email-templates.create', 'email-templates.edit', 'email-templates.delete'],
        ];

        $modules = config('modules', []);
        foreach ($modules as $module => $enabled) {
            if ($enabled) {
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
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $admin->givePermissionTo([
            'users.view', 'users.create', 'users.edit',
            'roles.view', 'roles.create', 'roles.edit',
            'permissions.view',
            'settings.view', 'settings.edit',
            'logs.view',
            'services.view', 'services.create', 'services.edit',
            'projects.view', 'projects.create', 'projects.edit',
            'testimonials.view', 'testimonials.create', 'testimonials.edit',
            'faqs.view', 'faqs.create', 'faqs.edit',
            'contact-messages.view', 'contact-messages.create', 'contact-messages.edit',
            'email-templates.view', 'email-templates.create', 'email-templates.edit',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo([
            'settings.view',
        ]);
    }
}