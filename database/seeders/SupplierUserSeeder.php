<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupplierUser;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SupplierUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create supplier admin user
        $supplierAdmin = SupplierUser::create([
            'supplier_id' => 1,
            'name' => 'Supplier Admin',
            'email' => 'admin@supplier1.com',
            'password' => Hash::make('password'),
            'status' => true,
        ]);

        // Create supplier staff user
        $supplierStaff = SupplierUser::create([
            'supplier_id' => 1,
            'name' => 'Supplier Staff',
            'email' => 'staff@supplier1.com',
            'password' => Hash::make('password'),
            'status' => true,
        ]);

        // Assign roles after user creation
        $this->assignRoles($supplierAdmin, $supplierStaff);
    }

    /**
     * Assign roles to supplier users
     */
    private function assignRoles($supplierAdmin, $supplierStaff)
    {
        try {
            // Set team context for the supplier
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId(1); // First supplier ID
            }

            // Assign supplier-admin role
            $supplierAdminRole = Role::where('name', 'supplier-admin')
                ->where('guard_name', 'supplier')
                ->where('team_id', 1)
                ->first();

            if ($supplierAdminRole) {
                $supplierAdmin->assignRole($supplierAdminRole);
                $this->command->info('Assigned supplier-admin role to: ' . $supplierAdmin->name);
            } else {
                $this->command->warn('supplier-admin role not found.');
            }

            // Assign supplier-staff role
            $supplierStaffRole = Role::where('name', 'supplier-staff')
                ->where('guard_name', 'supplier')
                ->where('team_id', 1)
                ->first();

            if ($supplierStaffRole) {
                $supplierStaff->assignRole($supplierStaffRole);
                $this->command->info('Assigned supplier-staff role to: ' . $supplierStaff->name);
            } else {
                $this->command->warn('supplier-staff role not found.');
            }

        } catch (\Exception $e) {
            $this->command->error('Error assigning supplier roles: ' . $e->getMessage());
        }
    }
}
