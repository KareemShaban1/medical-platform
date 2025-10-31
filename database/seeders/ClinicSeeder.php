<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clinic;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating clinics...');

        $clinics = [
            [
                'name' => 'Medical Excellence Clinic',
                'phone' => '0123456789',
                'address' => '123 Medical District, Cairo, Egypt',
                'is_allowed' => true,
                'status' => true,
            ],
            [
                'name' => 'City Health Center',
                'phone' => '0123456790',
                'address' => '456 Health Avenue, Alexandria, Egypt',
                'is_allowed' => true,
                'status' => true,
            ],
            [
                'name' => 'Prime Care Medical Center',
                'phone' => '0123456791',
                'address' => '789 Care Street, Giza, Egypt',
                'is_allowed' => true,
                'status' => true,
            ],
            [
                'name' => 'Family Wellness Clinic',
                'phone' => '0123456792',
                'address' => '321 Wellness Road, Mansoura, Egypt',
                'is_allowed' => true,
                'status' => true,
            ],
            [
                'name' => 'Advanced Medical Institute',
                'phone' => '0123456793',
                'address' => '654 Institute Boulevard, Tanta, Egypt',
                'is_allowed' => true,
                'status' => true,
            ],
        ];

        foreach ($clinics as $index => $clinicData) {
            $clinic = Clinic::create($clinicData);

            // Create approval record
            $clinic->approvement()->create([
                'module_id' => $clinic->id,
                'module_type' => 'App\Models\Clinic',
                'action_by' => 1,
                'action' => 'approved',
                'notes' => 'Approved by admin - Initial setup',
            ]);

            $this->command->info("Created: {$clinic->name}");
        }

        $this->command->info("✓ Successfully created " . count($clinics) . " clinics");
    }
}
