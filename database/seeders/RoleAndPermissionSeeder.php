<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Admin;
use App\Models\Clinic;
use App\Models\Supplier;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $basePermissions = [
            'view users','create user','update user','delete user',
            'view roles','create role','update role','delete role',
            'view settings','update settings'
        ];

        // Admin guard - uses fixed team ID
        $this->createGuardRolesAndPermissions(
            'admin',
            Admin::TeamId, // Use the constant from Admin model
            array_merge($basePermissions, [
                'view activity logs','create activity log','update activity log','delete activity log',
                'view doctor profiles','approve doctor profile','reject doctor profile','toggle featured doctor profile',
                'toggle lock doctor profile',
            ]),
            [
                'admin' => 'all',
            ]
        );

        // Get the first clinic for team-based permissions
        $firstClinic = Clinic::first();
        if ($firstClinic) {
            $this->command->info("Creating roles for first clinic (ID: {$firstClinic->id})");
            $this->createGuardRolesAndPermissions(
                'clinic',
                $firstClinic->id,
                array_merge($basePermissions, [
                    'view patients','create patient','update patient','delete patient',
                    'view appointments','create appointment','update appointment','delete appointment',
                    'view doctor profiles','create doctor profile','update doctor profile','delete doctor profile',
                    'submit doctor profile for review',
                ]),
                [
                    'clinic-admin' => ['view users','create user','update user','view patients','create patient','update patient','view appointments','create appointment',
                                     'view doctor profiles','create doctor profile','update doctor profile','submit doctor profile for review'],
                    'doctor' => ['view doctor profiles','create doctor profile','update doctor profile','submit doctor profile for review'],
                ]
            );
        } else {
            $this->command->warn('No clinic found. Clinic roles and permissions will not be created.');
        }

        // Get the first supplier for team-based permissions
        $firstSupplier = Supplier::first();
        if ($firstSupplier) {
            $this->command->info("Creating roles for first supplier (ID: {$firstSupplier->id})");
            $this->createGuardRolesAndPermissions(
                'supplier',
                $firstSupplier->id,
                array_merge($basePermissions, [
                    'view products','create product','update product','delete product',
                    'view orders','update order',
                ]),
                [
                    'supplier-admin' => ['view products','create product','update product','view orders','update order'],
                    'supplier-staff' => ['view products','view orders'],
                ]
            );
        } else {
            $this->command->warn('No supplier found. Supplier roles and permissions will not be created.');
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }

    protected function createGuardRolesAndPermissions(string $guard, int $teamId, array $permissions, array $rolesWithPerms): void
    {
        try {
            // Set the team context for permissions if the function exists
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($teamId);
            }

            $this->command->info("Creating permissions for {$guard} guard with team ID {$teamId}");

            foreach ($permissions as $perm) {
                Permission::firstOrCreate([
                    'name' => $perm,
                    'guard_name' => $guard
                ]);
            }

            $this->command->info("Creating roles for {$guard} guard");

            foreach ($rolesWithPerms as $roleName => $perms) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guard,
                    'team_id' => $teamId
                ]);

                if ($perms === 'all') {
                    $allPermissions = Permission::where('guard_name', $guard)->get();
                    $role->syncPermissions($allPermissions);
                    $this->command->info("Assigned all permissions to role: {$roleName}");
                } else {
                    $role->syncPermissions($perms);
                    $this->command->info("Assigned " . count($perms) . " permissions to role: {$roleName}");
                }
            }

        } catch (\Exception $e) {
            $this->command->error("Error creating roles/permissions for {$guard}: " . $e->getMessage());
            throw $e;
        }
    }
}
