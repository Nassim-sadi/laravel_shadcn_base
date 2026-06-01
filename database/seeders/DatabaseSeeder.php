<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            ContentSeeder::class,
            SuperAdminSeeder::class,
            BookingServiceSeeder::class,
            BookingSeeder::class,
            BookingSettingsSeeder::class,
        ]);
    }
}
