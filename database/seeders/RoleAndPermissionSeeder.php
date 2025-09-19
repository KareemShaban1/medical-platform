<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

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

        // Admin guard
        $this->createGuardRolesAndPermissions(
            'admin',
            array_merge($basePermissions, [
                'view activity logs','create activity log','update activity log','delete activity log',
            ]),
            [
                'admin' => 'all',
            ]
        );

        // Supplier guard
        $this->createGuardRolesAndPermissions(
            'supplier',
            array_merge($basePermissions, [
                'view products','create product','update product','delete product',
                'view orders','update order',
            ]),
            [
                'supplier' => ['view products','create product','update product','view orders','update order'],
            ]
        );

        // Clinic guard
        $this->createGuardRolesAndPermissions(
            'clinic',
            array_merge($basePermissions, [
                'view patients','create patient','update patient','delete patient',
                'view appointments','create appointment','update appointment','delete appointment',
            ]),
            [
                'clinic' => ['view patients','create patient','update patient','view appointments','create appointment'],
            ]
        );
    }

    protected function createGuardRolesAndPermissions(string $guard, array $permissions, array $rolesWithPerms): void
    {
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
        }

        foreach ($rolesWithPerms as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);

            if ($perms === 'all') {
                $role->syncPermissions(Permission::where('guard_name', $guard)->get());
            } else {
                $role->syncPermissions($perms);
            }
        }
    }
}
