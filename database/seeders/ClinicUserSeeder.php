<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClinicUser;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ClinicUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create clinic admin user
        $clinicAdmin = ClinicUser::create([
            'clinic_id' => 1,
            'name' => 'Clinic Admin',
            'email' => 'admin@clinic1.com',
            'password' => Hash::make('password'),
            'phone' => '01034113921',
            'status' => true,
            'salary_frequency' => 'monthly',
            'amount_per_salary_frequency' => 2000,
        ]);

        // Create doctor user
        $doctor = ClinicUser::create([
            'clinic_id' => 1,
            'name' => 'Dr. John Smith',
            'email' => 'doctor@clinic1.com',
            'password' => Hash::make('password'),
            'status' => true,
            'phone' => '01264313921',
            'salary_frequency' => 'monthly',
            'amount_per_salary_frequency' => 5000,
        ]);

        // Assign roles after user creation (roles should be created by RoleAndPermissionSeeder)
        // Note: This will be handled in a separate method since roles might not exist yet
        $this->assignRoles($clinicAdmin, $doctor);
    }

    /**
     * Assign roles to clinic users
     */
    private function assignRoles($clinicAdmin, $doctor)
    {
        try {
            // Set team context for the clinic
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId(1); // First clinic ID
            }

            // Assign clinic-admin role
            $clinicAdminRole = Role::where('name', 'clinic-admin')
                ->where('guard_name', 'clinic')
                ->where('team_id', 1)
                ->first();

            if ($clinicAdminRole) {
                $clinicAdmin->assignRole($clinicAdminRole);
                $this->command->info('Assigned clinic-admin role to: ' . $clinicAdmin->name);
            } else {
                $this->command->warn('clinic-admin role not found. Make sure RoleAndPermissionSeeder runs before this seeder.');
            }

            // Assign doctor role
            $doctorRole = Role::where('name', 'doctor')
                ->where('guard_name', 'clinic')
                ->where('team_id', 1)
                ->first();

            if ($doctorRole) {
                $doctor->assignRole($doctorRole);
                $this->command->info('Assigned doctor role to: ' . $doctor->name);
            } else {
                $this->command->warn('doctor role not found. Make sure RoleAndPermissionSeeder runs before this seeder.');
            }

        } catch (\Exception $e) {
            $this->command->error('Error assigning roles: ' . $e->getMessage());
        }
    }
}
