<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class BookingSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'booking.time_slot_style',
                'group' => 'booking',
                'value' => 'wheel',
                'default_value' => 'wheel',
                'type' => 'string',
                'name' => 'Time Slot Style',
                'description' => 'Display style for time slots: wheel or list',
                'is_public' => false,
            ],
            [
                'key' => 'booking.allow_duplicate_phone',
                'group' => 'booking',
                'value' => '0',
                'default_value' => '0',
                'type' => 'boolean',
                'name' => 'Allow Duplicate Phone',
                'description' => 'Allow multiple bookings with the same phone number on the same date',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
