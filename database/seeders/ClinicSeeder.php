<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Clinic;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $Clinic = Clinic::create([
            'name' => 'Clinic 1',
            'phone' => '1234567890',
            'address' => '1234567890',
            'is_allowed' => true,
            'status' => true,            
        ]);

        // approvement
        $Clinic->approvement()->create([
            'module_id' => $Clinic->id,
            'module_type' => 'App\Models\Clinic',
            'action_by' => 1,
            'action' => 'approved',
            'notes' => 'Approved by admin',
        ]);


        Clinic::factory()->count(20)->create();


    }
}