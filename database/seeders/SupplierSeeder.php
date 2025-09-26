<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $supplier = Supplier::create([
            'name' => 'Supplier 1',
            'phone' => '1234567890',
            'address' => '1234567890',
            'is_allowed' => true,
            'status' => true,
        ]);

        $supplier->approvement()->create([
            'module_id' => $supplier->id,
            'module_type' => 'App\Models\Supplier',
            'action_by' => 1,
            'action' => 'approved',
            'notes' => 'Approved by admin',
        ]);

        $suppliers = Supplier::factory()->count(20)->create();

        foreach ($suppliers as $supplier) {
            $supplier->approvement()->create([
                'module_id' => $supplier->id,
                'module_type' => 'App\Models\Supplier',
                'action_by' => 1,
                'action' => 'approved',
                'notes' => 'Approved by admin',
            ]);
        }
    }
}