<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'supplier_id' => Supplier::inRandomOrder()->first()->id,
            'name_en' => fake()->name(),
            'name_ar' => fake()->name(),
            'description_en' => fake()->text(),
            'description_ar' => fake()->text(),
            'sku' => fake()->unique()->word(),
            'price_before' => fake()->randomFloat(2, 10, 100),
            'price_after' => fake()->randomFloat(2, 10, 100),
            'discount_value' => fake()->randomFloat(2, 10, 100),
            'stock' => fake()->randomNumber(2),
            'approved' => true,
            'reason' => fake()->text(),
            'status' => true,
        ];
    }
}
