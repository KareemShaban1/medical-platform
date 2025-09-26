<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogCategory>
 */
class BlogCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name_en' => $this->faker->unique()->word,
            'name_ar' => $this->faker->unique()->word,
            'slug_ar' => $this->faker->unique()->slug,
            'slug_en' => $this->faker->unique()->slug,
            'status' => $this->faker->boolean,
        ];
    }
}
