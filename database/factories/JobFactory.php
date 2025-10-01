<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
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
            'title' => fake()->sentence(),
            'type' => fake()->randomElement(['full-time', 'part-time', 'contract','temporary', 'internship']),
            'description' => fake()->text(),
            'location' => fake()->city(),
            'salary' => fake()->numberBetween(1000, 10000),
            'status' => fake()->boolean(),
            'clinic_id' => Clinic::inRandomOrder()->first()->id,
        ];
    }
}