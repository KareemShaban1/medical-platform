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
        Supplier::create([
            'name' => 'Supplier 1',
            'phone' => '1234567890',
            'address' => '1234567890',
            'is_allowed' => true,
            'status' => true,
        ]);
    }
}