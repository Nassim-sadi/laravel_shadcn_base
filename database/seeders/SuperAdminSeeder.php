<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        $role = Role::firstOrCreate(['name' => 'super_admin']);

        $user = User::firstOrCreate(
            ['email' => 'nacimbreeze@gmail.com'],
            [
                'name' => 'Nacim Breeze',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'locale' => 'en',
                'email_verified_at' => now(),
            ],
        );

        if (! $user->hasRole('super_admin')) {
            $user->assignRole($role);
        }

        $role->syncPermissions(Permission::all());

        $this->command->info('Super admin seeded: nacimbreeze@gmail.com / password');
    }
}
