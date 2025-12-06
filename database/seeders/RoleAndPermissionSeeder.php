<?php

namespace Database\Seeders;

use Database\Seeders\Guards\AdminRolePermissionSeeder;
use Database\Seeders\Guards\ClinicRolePermissionSeeder;
use Database\Seeders\Guards\SupplierRolePermissionSeeder;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting roles and permissions seeding...');

        // Clear permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Clear existing roles and permissions
        $this->command->info('Clearing existing roles and permissions...');

        // Clear roles (this will also clear role_permissions pivot table)
        Role::query()->delete();

        // Clear permissions
        Permission::query()->delete();

        $this->command->info('Existing roles and permissions cleared.');

        // Seed Admin roles and permissions
        $this->command->info('Seeding Admin roles and permissions...');
        $this->call(AdminRolePermissionSeeder::class);
        $this->call(SystemAdminSeeder::class);

        // Seed Clinic roles and permissions
        $this->command->info('Seeding Clinic roles and permissions...');
        $this->call(ClinicRolePermissionSeeder::class);
        $this->call(ClinicUserSeeder::class);

        // Seed Supplier roles and permissions
        $this->command->info('Seeding Supplier roles and permissions...');
        $this->call(SupplierRolePermissionSeeder::class);
        $this->call(SupplierUserSeeder::class);

        // Clear permission cache again after seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('All roles and permissions seeded successfully!');
    }
}