<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'blog_category_id' => BlogCategory::inRandomOrder()->first()->id,
            'title_en' => fake()->sentence(),
            'title_ar' => fake()->sentence(),
            'slug_en' => fake()->unique()->slug(),
            'slug_ar' => fake()->unique()->slug(),
            'content_en' => fake()->text(),
            'content_ar' => fake()->text(),
            'status' => fake()->boolean(),
        ];
    }
}