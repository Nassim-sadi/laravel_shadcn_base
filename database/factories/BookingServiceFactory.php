<?php

namespace Database\Factories;

use App\Models\BookingService;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingServiceFactory extends Factory
{
    protected $model = BookingService::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);
        return [
            'name' => ['fr' => $name, 'en' => fake()->words(2, true), 'ar' => $name],
            'description' => ['fr' => fake()->sentence(), 'en' => fake()->sentence(), 'ar' => fake()->sentence()],
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'price' => fake()->randomFloat(2, 10, 200),
            'is_active' => true,
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
