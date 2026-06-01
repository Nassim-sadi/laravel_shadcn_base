<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingService;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $services = BookingService::all();

        if ($services->isEmpty()) {
            return;
        }

        $bookings = [
            [
                'service_key' => 'Haircut & Styling',
                'date' => now()->format('Y-m-d'),
                'start_time' => '09:00',
                'end_time' => '09:45',
                'customer_name' => 'Ahmed Benali',
                'customer_phone' => '+212600000001',
                'status' => 'confirmed',
            ],
            [
                'service_key' => 'Beard Trim',
                'date' => now()->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '10:20',
                'customer_name' => 'Youssef Amrani',
                'customer_phone' => '+212600000002',
                'status' => 'pending',
            ],
            [
                'service_key' => 'Full Grooming Package',
                'date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '11:00',
                'end_time' => '12:30',
                'customer_name' => 'Karim Tazi',
                'customer_phone' => '+212600000003',
                'status' => 'pending',
            ],
            [
                'service_key' => 'Haircut & Styling',
                'date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '14:45',
                'customer_name' => 'Omar Fassi',
                'customer_phone' => '+212600000004',
                'status' => 'completed',
            ],
            [
                'service_key' => 'Consultation',
                'date' => now()->addDays(2)->format('Y-m-d'),
                'start_time' => '09:00',
                'end_time' => '09:15',
                'customer_name' => 'Hassan El Amrani',
                'customer_phone' => '+212600000005',
                'status' => 'pending',
            ],
            [
                'service_key' => 'Beard Trim',
                'date' => now()->addDays(2)->format('Y-m-d'),
                'start_time' => '15:00',
                'end_time' => '15:20',
                'customer_name' => 'Mehdi Berrada',
                'customer_phone' => '+212600000006',
                'status' => 'cancelled',
            ],
            [
                'service_key' => 'Full Grooming Package',
                'date' => now()->addDays(3)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '11:30',
                'customer_name' => 'Rachid Alaoui',
                'customer_phone' => '+212600000007',
                'status' => 'confirmed',
            ],
            [
                'service_key' => 'Haircut & Styling',
                'date' => now()->addDays(3)->format('Y-m-d'),
                'start_time' => '13:00',
                'end_time' => '13:45',
                'customer_name' => 'Samir Idrissi',
                'customer_phone' => '+212600000008',
                'status' => 'pending',
            ],
        ];

        foreach ($bookings as $bookingData) {
            $service = BookingService::where('name->en', $bookingData['service_key'])->first();

            if (!$service) {
                continue;
            }

            Booking::create([
                'booking_service_id' => $service->id,
                'date' => $bookingData['date'],
                'start_time' => $bookingData['start_time'],
                'end_time' => $bookingData['end_time'],
                'customer_name' => $bookingData['customer_name'],
                'customer_phone' => $bookingData['customer_phone'],
                'status' => $bookingData['status'],
            ]);
        }
    }
}
