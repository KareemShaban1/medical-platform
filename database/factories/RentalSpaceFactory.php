<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Clinic;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalSpace>
 */
class RentalSpaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::inRandomOrder()->first()->id,
            'name' => fake()->company(),
            'location' => fake()->address(),
            'description' => fake()->paragraph(),
            'status' => true,
        ];
    }
}
