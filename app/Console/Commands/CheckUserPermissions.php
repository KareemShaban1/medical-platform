<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClinicUser;
use App\Models\SupplierUser;
use App\Models\Admin;

class CheckUserPermissions extends Command
{
    protected $signature = 'permissions:check-user {email} {--guard=clinic}';
    protected $description = 'Check user permissions and roles for debugging';

    public function handle()
    {
        $email = $this->argument('email');
        $guard = $this->option('guard');

        $this->info("Checking permissions for user: {$email} (guard: {$guard})");

        if ($guard === 'clinic') {
            $user = ClinicUser::where('email', $email)->first();
            if (!$user) {
                $this->error("User not found!");
                return 1;
            }

            $this->info("User ID: {$user->id}");
            $this->info("Clinic ID: {$user->clinic_id}");
            
            if (!$user->clinic_id) {
                $this->error("User has no clinic_id! Cannot check permissions.");
                return 1;
            }

            setPermissionsTeamId($user->clinic_id);
            $this->info("Team ID set to: {$user->clinic_id}");

        } elseif ($guard === 'supplier') {
            $user = SupplierUser::where('email', $email)->first();
            if (!$user) {
                $this->error("User not found!");
                return 1;
            }

            $this->info("User ID: {$user->id}");
            $this->info("Supplier ID: {$user->supplier_id}");
            
            if (!$user->supplier_id) {
                $this->error("User has no supplier_id! Cannot check permissions.");
                return 1;
            }

            setPermissionsTeamId($user->supplier_id);
            $this->info("Team ID set to: {$user->supplier_id}");

        } elseif ($guard === 'admin') {
            $user = Admin::where('email', $email)->first();
            if (!$user) {
                $this->error("User not found!");
                return 1;
            }

            $this->info("User ID: {$user->id}");
            setPermissionsTeamId(Admin::TeamId);
            $this->info("Team ID set to: " . Admin::TeamId);

        } else {
            $this->error("Invalid guard: {$guard}");
            return 1;
        }

        // Check roles
        $this->info("\n=== ROLES ===");
        $roles = $user->roles;
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $this->info("Role: {$role->name} (ID: {$role->id}, Team ID: {$role->team_id})");
            }
        } else {
            $this->warn("User has NO roles assigned!");
        }

        // Check specific role
        $this->info("\n=== ROLE CHECKS ===");
        if ($guard === 'clinic') {
            $this->info("Has 'clinic-admin' role: " . ($user->hasRole('clinic-admin') ? 'YES' : 'NO'));
        } elseif ($guard === 'supplier') {
            $this->info("Has 'supplier-admin' role: " . ($user->hasRole('supplier-admin') ? 'YES' : 'NO'));
        } elseif ($guard === 'admin') {
            $this->info("Has 'admin' role: " . ($user->hasRole('admin') ? 'YES' : 'NO'));
        }

        // Check permissions
        $this->info("\n=== PERMISSIONS ===");
        $permissions = $user->getAllPermissions();
        $this->info("Total permissions: " . $permissions->count());
        
        // Check specific permission
        $this->info("\n=== PERMISSION CHECKS ===");
        $this->info("Can 'view dashboard': " . ($user->can('view dashboard') ? 'YES' : 'NO'));
        
        // List all permissions
        if ($permissions->count() > 0) {
            $this->info("\nAll permissions:");
            foreach ($permissions as $permission) {
                $this->line("  - {$permission->name}");
            }
        } else {
            $this->warn("User has NO permissions!");
        }

        // Check if permission exists
        $this->info("\n=== PERMISSION EXISTS CHECK ===");
        $permission = \Spatie\Permission\Models\Permission::where('name', 'view dashboard')
            ->where('guard_name', $guard)
            ->first();
        
        if ($permission) {
            $this->info("Permission 'view dashboard' EXISTS (ID: {$permission->id})");
        } else {
            $this->error("Permission 'view dashboard' DOES NOT EXIST for guard '{$guard}'!");
        }

        return 0;
    }
}












