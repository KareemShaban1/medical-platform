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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info('Clearing existing roles and permissions...');

        $this->command->info('Starting roles and permissions seeding...');



        $this->command->info('Existing roles and permissions cleared.');

        // Seed Clinic roles and permissions
        $this->command->info('Seeding Clinic roles and permissions...');
        $this->call(ClinicRolePermissionSeeder::class);
        $this->call(ClinicUserSeeder::class);

        // Seed Supplier roles and permissions
        $this->command->info('Seeding Supplier roles and permissions...');
        $this->call(SupplierRolePermissionSeeder::class);
        $this->call(SupplierUserSeeder::class);

        $this->command->info('All roles and permissions seeded successfully!');
    }
}
