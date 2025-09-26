<?php

namespace Database\Factories;

use App\Models\RentalSpace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalPricing>
 */
class RentalPricingFactory extends Factory
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
            'price' => fake()->randomFloat(2, 100, 1000),
            'notes' => fake()->paragraph(),
        ];
    }
}
