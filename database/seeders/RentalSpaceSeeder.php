<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RentalSpace;
use App\Models\RentalAvailability;
use App\Models\RentalPricing;

class RentalSpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $rentalSpaces = RentalSpace::factory()->count(30)->create();
        // RentalAvailability::factory()->count(30)->create();
        // RentalPricing::factory()->count(30)->create();

        foreach ($rentalSpaces as $rentalSpace) {
            $rentalSpace->pricing()->create([
                'price' => fake()->randomFloat(2, 100, 1000),
                'notes' => fake()->paragraph(),
            ]);
            $rentalSpace->availability()->create([
                'type' => fake()->randomElement(['daily', 'weekly', 'monthly']),
                'from_time' => fake()->time(),
                'to_time' => fake()->time(),
                'from_day' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                'to_day' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                'from_date'=>fake()->date(),
                'to_date'=>fake()->date(),
                'notes'=>fake()->paragraph(),
                'status'=>true,
            ]);
            $rentalSpace->approvement()->create([
                'module_type' => RentalSpace::class,
                'module_id' => $rentalSpace->id,
                'action_by' => 1,
                'action' => 'approved',
                'notes' => 'Approved by admin',
            ]);
        }
    }
    
}
