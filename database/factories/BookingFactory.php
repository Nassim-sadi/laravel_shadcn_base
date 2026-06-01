<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingService;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'booking_service_id' => BookingService::factory(),
            'date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'start_time' => fake()->randomElement(['09:00', '10:00', '11:00', '14:00', '15:00']),
            'end_time' => fake()->randomElement(['09:30', '10:30', '11:30', '14:30', '15:30']),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }
}
