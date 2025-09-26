<?php

namespace Database\Factories;

use App\Models\RentalSpace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalAvailability>
 */
class RentalAvailabilityFactory extends Factory
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
            'rental_space_id' => RentalSpace::inRandomOrder()->first()->id,
            'type' => fake()->randomElement(['daily', 'weekly', 'monthly']),
            'from_time' => fake()->time(),
            'to_time' => fake()->time(),
            'from_day' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'to_day' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'from_date'=>fake()->date(),
            'to_date'=>fake()->date(),
            'notes'=>fake()->paragraph(),
            'status'=>true,
        ];
    }
}
