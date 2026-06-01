<?php

namespace Database\Seeders;

use App\Models\AvailabilityRule;
use App\Models\BookingService;
use Illuminate\Database\Seeder;

class BookingServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => [
                    'en' => 'Haircut & Styling',
                    'fr' => 'Coupe & Coiffage',
                    'ar' => 'قص وتصفيف الشعر',
                ],
                'description' => [
                    'en' => 'Professional haircut with styling consultation.',
                    'fr' => 'Coupe professionnelle avec consultation de coiffage.',
                    'ar' => 'قص شعر احترافي مع استشارة التصفيف.',
                ],
                'duration_minutes' => 45,
                'price' => 35.00,
                'is_active' => true,
                'order' => 1,
                'availability' => [
                    1 => ['09:00', '18:00'],
                    2 => ['09:00', '18:00'],
                    3 => ['09:00', '18:00'],
                    4 => ['09:00', '18:00'],
                    5 => ['09:00', '18:00'],
                    6 => ['09:00', '14:00'],
                ],
            ],
            [
                'name' => [
                    'en' => 'Beard Trim',
                    'fr' => 'Taille de Barbe',
                    'ar' => 'تهذيب اللحية',
                ],
                'description' => [
                    'en' => 'Precise beard trimming and shaping.',
                    'fr' => 'Taille et modelage précis de la barbe.',
                    'ar' => 'تهذيب وتشكيل دقيق للحية.',
                ],
                'duration_minutes' => 20,
                'price' => 15.00,
                'is_active' => true,
                'order' => 2,
                'availability' => [
                    1 => ['09:00', '18:00'],
                    2 => ['09:00', '18:00'],
                    3 => ['09:00', '18:00'],
                    4 => ['09:00', '18:00'],
                    5 => ['09:00', '18:00'],
                    6 => ['09:00', '14:00'],
                ],
            ],
            [
                'name' => [
                    'en' => 'Full Grooming Package',
                    'fr' => 'Forfait Toilettage Complet',
                    'ar' => 'باقة العناية الكاملة',
                ],
                'description' => [
                    'en' => 'Complete grooming: haircut, beard trim, and facial treatment.',
                    'fr' => 'Toilettage complet : coupe, barbe et soin facial.',
                    'ar' => 'عناية كاملة: قص شعر، لحية، وعناية بالوجه.',
                ],
                'duration_minutes' => 90,
                'price' => 75.00,
                'is_active' => true,
                'order' => 3,
                'availability' => [
                    1 => ['10:00', '17:00'],
                    2 => ['10:00', '17:00'],
                    3 => ['10:00', '17:00'],
                    4 => ['10:00', '17:00'],
                    5 => ['10:00', '17:00'],
                    6 => ['10:00', '14:00'],
                ],
            ],
            [
                'name' => [
                    'en' => 'Consultation',
                    'fr' => 'Consultation',
                    'ar' => 'استشارة',
                ],
                'description' => [
                    'en' => 'Free 15-minute consultation to discuss your needs.',
                    'fr' => 'Consultation gratuite de 15 minutes pour discuter de vos besoins.',
                    'ar' => 'استشارة مجانية لمدة 15 دقيقة لمناقشة احتياجاتك.',
                ],
                'duration_minutes' => 15,
                'price' => 0,
                'is_active' => true,
                'order' => 4,
                'availability' => [
                    1 => ['09:00', '17:00'],
                    2 => ['09:00', '17:00'],
                    3 => ['09:00', '17:00'],
                    4 => ['09:00', '17:00'],
                    5 => ['09:00', '17:00'],
                    6 => ['10:00', '13:00'],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $availability = $serviceData['availability'] ?? [];
            unset($serviceData['availability']);

            $service = BookingService::updateOrCreate(
                ['name->en' => $serviceData['name']['en']],
                $serviceData
            );

            foreach ($availability as $dayOfWeek => $times) {
                AvailabilityRule::updateOrCreate(
                    [
                        'booking_service_id' => $service->id,
                        'day_of_week' => $dayOfWeek,
                    ],
                    [
                        'start_time' => $times[0],
                        'end_time' => $times[1],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
