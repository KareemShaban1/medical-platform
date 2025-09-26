<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title_en' => fake()->sentence(),
            'title_ar' => fake()->sentence(),
            'slug_en' => fake()->slug(),
            'slug_ar' => fake()->slug(),
            'description_en' => fake()->text(),
            'description_ar' => fake()->text(),
            'url' => fake()->url(),
            'duration' => fake()->numberBetween(1, 10),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => fake()->boolean(),
        ];
    }
}
