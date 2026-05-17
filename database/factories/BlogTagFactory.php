<?php

namespace Database\Factories;

use App\Models\BlogTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogTagFactory extends Factory
{
    protected $model = BlogTag::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => ucfirst($name),
            'slug' => str($name)->slug(),
        ];
    }
}
