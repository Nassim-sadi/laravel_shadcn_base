<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => str($title)->slug(),
            'excerpt' => fake()->text(200),
            'is_published' => fake()->boolean(80),
            'featured' => fake()->boolean(20),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'category_id' => null,
        ];
    }
}
