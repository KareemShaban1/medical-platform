<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Clearing existing admin users...');
        $this->clearExistingData();

        $this->command->info('Creating admin users...');

        // Create system admin
        $systemAdmin = Admin::create([
            'name' => 'System Admin',
            'email' => 'system@medical-platform.com',
            'password' => Hash::make('system123456'),
            'status' => true,
        ]);

        $systemAdmin->assignRole('super-admin');
        $this->command->info('  ✓ Created system admin');

        // Create admin user
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'status' => true,
        ]);
        $admin->assignRole('admin');
        $this->command->info('  ✓ Created admin user');

        $this->command->info('✓ Successfully created admin users');
    }

    /**
     * Clear existing admin user data
     */
    private function clearExistingData(): void
    {
        $this->command->info('  Deleting admin users (including soft-deleted)...');

        // Clear role assignments first
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId(Admin::TeamId);
        }

        // Delete all admin users including soft-deleted ones
        $deletedCount = Admin::withTrashed()->forceDelete();

        $this->command->info("  ✓ Deleted {$deletedCount} admin user(s) and their related data");
    }
}
