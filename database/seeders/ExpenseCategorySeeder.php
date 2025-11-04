<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinics = \App\Models\Clinic::all();
        if ($clinics->isEmpty()) {
            $this->command->warn('No clinics found. Run ClinicSeeder first.');
            return;
        }

        $defaultCategories = [
            'Rent', 'Utilities', 'Salaries', 'Supplies', 'Maintenance', 'Equipment', 'Marketing', 'Insurance', 'Cleaning', 'Miscellaneous'
        ];

        foreach ($clinics as $clinic) {
            foreach ($defaultCategories as $cat) {
                \App\Models\ExpenseCategory::firstOrCreate(
                    ['clinic_id' => $clinic->id, 'name' => $cat],
                    ['status' => true]
                );
            }
        }
        $this->command->info('Expense categories seeded for '.count($clinics).' clinics.');
    }
}
