<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClinicUser;
use App\Models\SupplierUser;
use App\Models\Clinic;
use App\Models\Supplier;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Services\RolePermissionService;

class FixUserRoles extends Command
{
    protected $signature = 'permissions:fix-user-roles {--guard=clinic} {--email=}';
    protected $description = 'Fix role assignments for users by re-assigning with correct team_id';

    public function handle()
    {
        $guard = $this->option('guard');
        $email = $this->option('email');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($guard === 'clinic') {
            $this->fixClinicUsers($email);
        } elseif ($guard === 'supplier') {
            $this->fixSupplierUsers($email);
        } else {
            $this->error("Invalid guard. Use 'clinic' or 'supplier'");
            return 1;
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info("\nDone! Permission cache cleared.");
        return 0;
    }

    protected function fixClinicUsers(?string $email = null)
    {
        $query = ClinicUser::query();

        if ($email) {
            $query->where('email', $email);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->warn("No clinic users found" . ($email ? " with email: {$email}" : ""));
            return;
        }

        $this->info("Found {$users->count()} clinic user(s) to process...\n");

        foreach ($users as $user) {
            $this->info("Processing: {$user->email} (ID: {$user->id}, Clinic ID: {$user->clinic_id})");

            if (!$user->clinic_id) {
                $this->warn("  ⚠️  User has no clinic_id, skipping...");
                continue;
            }

            setPermissionsTeamId($user->clinic_id);

            // Get current roles
            $currentRoles = $user->roles;
            $this->info("  Current roles: " . ($currentRoles->count() > 0 ? $currentRoles->pluck('name')->join(', ') : 'None'));

            // Find clinic-admin role for this clinic
            $adminRole = Role::where('name', 'clinic-admin')
                ->where('guard_name', 'clinic')
                ->where('team_id', $user->clinic_id)
                ->first();

            if (!$adminRole) {
                // Create role if it doesn't exist using service
                $clinic = Clinic::find($user->clinic_id);
                if ($clinic) {
                    $this->info("  🔧 Creating 'clinic-admin' role for clinic ID {$user->clinic_id}...");
                    $rolePermissionService = app(RolePermissionService::class);
                    $adminRole = $rolePermissionService->createClinicRolesAndPermissions($clinic);
                } else {
                    $this->warn("  ⚠️  Clinic ID {$user->clinic_id} not found, skipping...");
                    continue;
                }
            }

            // Re-assign role
            $user->syncRoles([$adminRole]);
            $this->info("  ✅ Assigned role: {$adminRole->name}");

            // Verify
            setPermissionsTeamId($user->clinic_id);
            $hasRole = $user->hasRole('clinic-admin');
            $hasPermission = $user->can('view dashboard');

            $this->info("  Verification:");
            $this->info("    - Has 'clinic-admin' role: " . ($hasRole ? 'YES ✅' : 'NO ❌'));
            $this->info("    - Can 'view dashboard': " . ($hasPermission ? 'YES ✅' : 'NO ❌'));

            if (!$hasRole || !$hasPermission) {
                $this->warn("  ⚠️  Role/permission check failed. The role might not have the permission assigned.");
            }

            $this->newLine();
        }
    }

    protected function fixSupplierUsers(?string $email = null)
    {
        $query = SupplierUser::query();

        if ($email) {
            $query->where('email', $email);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->warn("No supplier users found" . ($email ? " with email: {$email}" : ""));
            return;
        }

        $this->info("Found {$users->count()} supplier user(s) to process...\n");

        foreach ($users as $user) {
            $this->info("Processing: {$user->email} (ID: {$user->id}, Supplier ID: {$user->supplier_id})");

            if (!$user->supplier_id) {
                $this->warn("  ⚠️  User has no supplier_id, skipping...");
                continue;
            }

            setPermissionsTeamId($user->supplier_id);

            // Get current roles
            $currentRoles = $user->roles;
            $this->info("  Current roles: " . ($currentRoles->count() > 0 ? $currentRoles->pluck('name')->join(', ') : 'None'));

            // Find supplier-admin role for this supplier
            $adminRole = Role::where('name', 'supplier-admin')
                ->where('guard_name', 'supplier')
                ->where('team_id', $user->supplier_id)
                ->first();

            if (!$adminRole) {
                // Create role if it doesn't exist using service
                $supplier = Supplier::find($user->supplier_id);
                if ($supplier) {
                    $this->info("  🔧 Creating 'supplier-admin' role for supplier ID {$user->supplier_id}...");
                    $rolePermissionService = app(RolePermissionService::class);
                    $adminRole = $rolePermissionService->createSupplierRolesAndPermissions($supplier);
                } else {
                    $this->warn("  ⚠️  Supplier ID {$user->supplier_id} not found, skipping...");
                    continue;
                }
            }

            // Re-assign role
            $user->syncRoles([$adminRole]);
            $this->info("  ✅ Assigned role: {$adminRole->name}");

            // Verify
            setPermissionsTeamId($user->supplier_id);
            $hasRole = $user->hasRole('supplier-admin');
            $hasPermission = $user->can('view dashboard');

            $this->info("  Verification:");
            $this->info("    - Has 'supplier-admin' role: " . ($hasRole ? 'YES ✅' : 'NO ❌'));
            $this->info("    - Can 'view dashboard': " . ($hasPermission ? 'YES ✅' : 'NO ❌'));

            if (!$hasRole || !$hasPermission) {
                $this->warn("  ⚠️  Role/permission check failed. The role might not have the permission assigned.");
            }

            $this->newLine();
        }
    }
}