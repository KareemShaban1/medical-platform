<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinics = \App\Models\Clinic::with('expenseCategories')->get();
        if ($clinics->isEmpty()) {
            $this->command->warn('No clinics found. Run ClinicSeeder first.');
            return;
        }

        $faker = \Faker\Factory::create();
        $totalCreated = 0;

        foreach ($clinics as $clinic) {
            $categories = \App\Models\ExpenseCategory::where('clinic_id', $clinic->id)->get();
            if ($categories->isEmpty()) { continue; }

            // Create 20-40 random expenses for the last 90 days per clinic
            $count = rand(20, 40);
            for ($i = 0; $i < $count; $i++) {
                $category = $categories->random();
                \App\Models\Expense::create([
                    'clinic_id' => $clinic->id,
                    'category_id' => $category->id,
                    'amount' => $faker->randomFloat(2, 50, 3000),
                    'expense_date' => $faker->dateTimeBetween('-90 days', 'now'),
                    'supplier_id' => null,
                    'notes' => $faker->sentence(6),
                ]);
                $totalCreated++;
            }
        }

        $this->command->info("Seeded {$totalCreated} expenses across ".count($clinics)." clinics.");
    }
}
