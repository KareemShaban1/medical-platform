<?php

namespace Database\Seeders\Guards;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Supplier;

class SupplierRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Get all suppliers and create roles/permissions for each
        $suppliers = Supplier::all();

        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Supplier roles and permissions will not be created.');
            return;
        }

        foreach ($suppliers as $supplier) {
            $this->seedForSupplier($supplier);
        }

        $this->command->info('Supplier roles and permissions seeded successfully for all suppliers!');
    }

    protected function seedForSupplier(Supplier $supplier): void
    {
        $teamId = $supplier->id;

        // Set the team context for permissions
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($teamId);
        }

        $this->command->info("Creating permissions for supplier guard with team ID {$teamId} (Supplier: {$supplier->name})");

        $permissionGroups = [
            'Dashboard' => ['view dashboard'],
            'Users' => ['view users', 'create user', 'update user', 'delete user', 'restore user', 'force delete user', 'toggle user status'],
            'Roles' => ['view roles', 'create role', 'update role', 'delete role', 'restore role', 'force delete role'],
            'Settings' => ['view settings', 'update settings', 'view supplier info', 'update supplier info'],
            'Products' => ['view products', 'create product', 'update product', 'delete product', 'restore product', 'force delete product', 'toggle product status'],
            'Offers' => ['view offers', 'create offer', 'update offer', 'delete offer', 'view available requests', 'create offer for request'],
            'Orders' => ['view orders', 'update order status', 'update order payment status', 'create refund', 'update refund status', 'view order analytics'],
            'Specialized Categories' => ['view specialized categories', 'create specialized category', 'update specialized category', 'delete specialized category', 'attach specialized category', 'view available categories'],
            'Subscriptions' => ['view subscriptions', 'subscribe', 'cancel subscription', 'view subscription usage'],
            'Notifications' => ['view notifications', 'mark notification as read', 'mark all notifications as read'],
            'Announcements' => ['dismiss announcement'],
        ];

        $permissions = [];
        foreach ($permissionGroups as $group => $perms) {
            $permissions = array_merge($permissions, $perms);
        }

        foreach ($permissionGroups as $group => $perms) {
            foreach ($perms as $permission) {
                Permission::updateOrCreate(
                    [
                        'name' => $permission,
                        'guard_name' => 'supplier',
                    ],
                    [
                        'group' => $group,
                    ]
                );
            }
        }

        $this->command->info("Creating roles for supplier guard (Team ID: {$teamId})");

        // Define roles with their permissions
        $roles = [
            'supplier-admin' => 'all', // All permissions
            'supplier-manager' => [
                'view dashboard',
                'view users',
                'create user',
                'update user',
                'view roles',
                'view settings',
                'update settings',
                'view supplier info',
                'update supplier info',
                'view products',
                'create product',
                'update product',
                'delete product',
                'toggle product status',
                'view offers',
                'create offer',
                'update offer',
                'delete offer',
                'view available requests',
                'create offer for request',
                'view orders',
                'update order status',
                'update order payment status',
                'create refund',
                'update refund status',
                'view order analytics',
                'view specialized categories',
                'create specialized category',
                'update specialized category',
                'delete specialized category',
                'attach specialized category',
                'view subscriptions',
                'subscribe',
                'cancel subscription',
                'view subscription usage',
                'view notifications',
                'mark notification as read',
                'mark all notifications as read',
                'dismiss announcement',
            ],
            'supplier-staff' => [
                'view dashboard',
                'view users',
                'view products',
                'create product',
                'update product',
                'view offers',
                'create offer',
                'update offer',
                'view available requests',
                'create offer for request',
                'view orders',
                'update order status',
                'view specialized categories',
                'view subscriptions',
                'view subscription usage',
                'view notifications',
                'mark notification as read',
                'mark all notifications as read',
                'dismiss announcement',
            ],
            'supplier-viewer' => [
                'view dashboard',
                'view users',
                'view products',
                'view offers',
                'view orders',
                'view specialized categories',
                'view subscriptions',
                'view subscription usage',
                'view notifications',
                'mark notification as read',
                'mark all notifications as read',
                'dismiss announcement',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'supplier',
                'team_id' => $teamId
            ]);

            if ($perms === 'all') {
                $allPermissions = Permission::where('guard_name', 'supplier')->get();
                $role->syncPermissions($allPermissions);
                $this->command->info("Assigned all permissions to role: {$roleName} for supplier {$supplier->name}");
            } else {
                $role->syncPermissions($perms);
                $this->command->info("Assigned " . count($perms) . " permissions to role: {$roleName} for supplier {$supplier->name}");
            }
        }
    }
}