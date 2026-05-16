<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $titleFr = fake()->words(3, true);
        return [
            'title' => ['fr' => $titleFr, 'en' => fake()->words(3, true), 'ar' => $titleFr],
            'description' => ['fr' => fake()->paragraph(), 'en' => fake()->paragraph(), 'ar' => fake()->paragraph()],
            'client' => ['fr' => fake()->company(), 'en' => fake()->company(), 'ar' => fake()->company()],
            'technologies' => [fake()->word(), fake()->word()],
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
