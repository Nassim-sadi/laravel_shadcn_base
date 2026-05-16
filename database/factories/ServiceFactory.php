<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $titleFr = fake()->words(3, true);
        return [
            'title' => ['fr' => $titleFr, 'en' => fake()->words(3, true), 'ar' => $titleFr],
            'description' => ['fr' => fake()->paragraph(), 'en' => fake()->paragraph(), 'ar' => fake()->paragraph()],
            'icon' => fake()->randomElement(['code', 'smartphone', 'search', 'globe', 'settings']),
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
