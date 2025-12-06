<?php

namespace Database\Seeders\Guards;

use App\Models\Clinic;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ClinicRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Get all clinics and create roles/permissions for each
        $clinics = Clinic::all();

        if ($clinics->isEmpty()) {
            $this->command->warn('No clinics found. Clinic roles and permissions will not be created.');

            return;
        }

        foreach ($clinics as $clinic) {
            $this->seedForClinic($clinic);
        }

        $this->command->info('Clinic roles and permissions seeded successfully for all clinics!');
    }

    protected function seedForClinic(Clinic $clinic): void
    {
        $teamId = $clinic->id;

        // Set the team context for permissions
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($teamId);
        }

        $this->command->info("Creating permissions for clinic guard with team ID {$teamId} (Clinic: {$clinic->name})");

        $permissionGroups = [
            'Dashboard' => ['view dashboard', 'view clinic info', 'update clinic info'],
            'Roles' => ['view roles', 'create role', 'update role', 'delete role', 'view trash roles', 'restore role', 'force delete role'],
            'Users' => ['view users', 'create user', 'update user', 'delete user', 'view trash users', 'restore user', 'force delete user', 'toggle user status'],
            'Doctor Profiles' => ['view doctor profiles', 'create doctor profile', 'update doctor profile', 'delete doctor profile', 'view trash doctor profiles', 'restore doctor profile', 'force delete doctor profile', 'submit doctor profile'],
            'Salary Contracts' => ['view salary contracts', 'create salary contract', 'update salary contract', 'delete salary contract', 'view trash salary contracts', 'restore salary contract', 'force delete salary contract'],
            'Payslips' => ['view payslips', 'create payslip', 'update payslip', 'delete payslip', 'view trash payslips', 'restore payslip', 'force delete payslip'],
            'Working Hours' => ['view working hours', 'create working hour', 'update working hour', 'delete working hour', 'bulk save working hours', 'view user working hours', 'view trash working hours'],
            'Attendance' => ['view attendance', 'check in', 'check out', 'mark absence', 'approve attendance', 'approve check in', 'approve check out', 'view my attendance logs', 'compute attendance', 'view trash attendance'],
            'Clinic Inventories' => ['view clinic inventories', 'create clinic inventory', 'update clinic inventory', 'delete clinic inventory', 'view trash clinic inventories', 'restore clinic inventory', 'force delete clinic inventory'],
            'Clinic Inventory Movements' => ['view clinic inventory movements', 'create clinic inventory movement', 'update clinic inventory movement', 'delete clinic inventory movement', 'view trash clinic inventory movements', 'restore clinic inventory movement', 'force delete clinic inventory movement'],
            'Rental Spaces' => ['view rental spaces', 'create rental space', 'update rental space', 'delete rental space', 'view trash rental spaces', 'restore rental space', 'force delete rental space', 'toggle rental space status'],
            'Jobs' => ['view jobs', 'create job', 'update job', 'delete job', 'view trash jobs', 'restore job', 'force delete job', 'toggle job status', 'view job applicants', 'update job application status', 'update job application data'],
            'Job Application Fields' => ['view job application fields', 'create job application field', 'update job application field', 'delete job application field', 'toggle job application field status'],
            'Purchase Requests' => ['view purchase requests', 'create purchase request', 'update purchase request', 'delete purchase request', 'view trash purchase requests', 'accept offer', 'cancel request', 'process offer payment'],
            'Orders' => ['view orders', 'update order', 'update order payment status', 'view trash orders'],
            'Course Enrollments' => ['view course enrollments', 'update course enrollment', 'delete course enrollment'],
            'Appointments' => ['view appointments', 'create appointment', 'update appointment', 'delete appointment', 'view trash appointments', 'restore appointment', 'force delete appointment', 'confirm appointment', 'cancel appointment'],
            'Availability Overrides' => ['view availability overrides', 'create availability override', 'update availability override', 'delete availability override', 'view trash availability overrides', 'restore availability override', 'force delete availability override', 'view doctor availability overrides'],
            'Daily Periods' => ['view daily periods', 'create daily period', 'update daily period', 'delete daily period', 'view daily period appointments', 'toggle daily period open', 'update daily period capacity', 'generate daily periods', 'view trash daily periods'],
            'Medical Records' => ['view medical records', 'update medical record', 'share medical record', 'view trash medical records'],
            'Patients' => ['view patients', 'create patient', 'update patient', 'delete patient', 'view trash patients', 'restore patient', 'force delete patient'],
            'Lab Orders' => ['view lab orders', 'create lab order', 'update lab order', 'upload lab order', 'complete lab order', 'view trash lab orders'],
            'Expense Categories' => ['view expense categories', 'create expense category', 'update expense category', 'delete expense category', 'view trash expense categories', 'restore expense category', 'force delete expense category', 'toggle expense category status'],
            'Expenses' => ['view expenses', 'create expense', 'update expense', 'delete expense', 'view trash expenses', 'restore expense', 'force delete expense'],
            'Invoices' => ['view invoices', 'create invoice', 'update invoice', 'mark invoice paid', 'add invoice item', 'update invoice item', 'delete invoice item', 'update invoice header', 'view trash invoices'],
            'Prescriptions' => ['view prescriptions', 'create prescription', 'update prescription', 'delete prescription', 'view trash prescriptions', 'print prescription', 'download prescription'],
            'Subscriptions' => ['view subscriptions', 'subscribe', 'cancel subscription', 'view subscription usage', 'view trash subscriptions'],
            'Notifications' => ['view notifications', 'mark notification as read', 'mark all notifications as read'],
            'Settings' => ['view settings', 'update settings'],
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
                        'guard_name' => 'clinic',
                    ],
                    [
                        'group' => $group,
                    ]
                );
            }
        }

        $this->command->info("Creating roles for clinic guard (Team ID: {$teamId})");

        // Define roles with their permissions
        $roles = [
            'clinic-admin' => 'all', // All permissions
            'doctor' => [
                'view dashboard',
                'view patients',
                'view appointments', 'create appointment', 'update appointment', 'delete appointment', 'confirm appointment', 'cancel appointment',
                'view doctor profiles', 'create doctor profile', 'update doctor profile', 'delete doctor profile', 'submit doctor profile',
                'view prescriptions', 'create prescription', 'update prescription', 'delete prescription', 'print prescription', 'download prescription',
                'view medical records', 'update medical record', 'share medical record',
                'view lab orders', 'create lab order', 'update lab order', 'upload lab order', 'complete lab order',
                'view invoices', 'create invoice', 'update invoice',
                'view availability overrides', 'create availability override', 'update availability override', 'delete availability override', 'view doctor availability overrides',
                'view daily periods', 'view daily period appointments', 'toggle daily period open', 'update daily period capacity',
                'view subscriptions', 'view subscription usage',
                'view notifications', 'mark notification as read', 'mark all notifications as read',
            ],
            'nurse' => [
                'view dashboard',
                'view patients',
                'view appointments',
                'view prescriptions',
                'view medical records',
                'view lab orders',
                'view notifications', 'mark notification as read', 'mark all notifications as read',
            ],
            'receptionist' => [
                'view dashboard',
                'view patients', 'create patient', 'update patient',
                'view appointments', 'create appointment', 'update appointment', 'confirm appointment', 'cancel appointment',
                'view daily periods', 'view daily period appointments',
                'view subscriptions', 'view subscription usage',
                'view notifications', 'mark notification as read', 'mark all notifications as read',
            ],
            'accountant' => [
                'view dashboard',
                'view salary contracts', 'create salary contract', 'update salary contract',
                'view payslips', 'create payslip', 'update payslip',
                'view expense categories', 'create expense category', 'update expense category', 'delete expense category',
                'view expenses', 'create expense', 'update expense', 'delete expense',
                'view invoices', 'create invoice', 'update invoice', 'mark invoice paid',
                'view subscriptions', 'view subscription usage',
                'view notifications', 'mark notification as read', 'mark all notifications as read',
            ],
            'inventory-manager' => [
                'view dashboard',
                'view clinic inventories', 'create clinic inventory', 'update clinic inventory', 'delete clinic inventory',
                'view clinic inventory movements', 'create clinic inventory movement', 'update clinic inventory movement', 'delete clinic inventory movement',
                'view purchase requests', 'create purchase request', 'update purchase request', 'delete purchase request', 'accept offer', 'cancel request',
                'view orders',
                'view subscriptions', 'view subscription usage',
                'view notifications', 'mark notification as read', 'mark all notifications as read',
            ],
            'hr-manager' => [
                'view dashboard',
                'view users', 'create user', 'update user', 'delete user', 'toggle user status',
                'view roles', 'create role', 'update role', 'delete role',
                'view jobs', 'create job', 'update job', 'delete job', 'toggle job status', 'view job applicants', 'update job application status', 'update job application data',
                'view job application fields', 'create job application field', 'update job application field', 'delete job application field',
                'view attendance', 'approve attendance', 'approve check in', 'approve check out', 'compute attendance',
                'view working hours', 'create working hour', 'update working hour', 'delete working hour', 'bulk save working hours', 'view user working hours',
                'view salary contracts', 'create salary contract', 'update salary contract',
                'view payslips', 'create payslip', 'update payslip',
                'view subscriptions', 'view subscription usage',
                'view notifications', 'mark notification as read', 'mark all notifications as read',
            ],
            'staff' => [
                'view dashboard',
                'view patients',
                'view appointments',
                'view notifications', 'mark notification as read', 'mark all notifications as read',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'clinic',
                'team_id' => $teamId,
            ]);

            if ($perms === 'all') {
                $allPermissions = Permission::where('guard_name', 'clinic')->get();
                $role->syncPermissions($allPermissions);
                $this->command->info("Assigned all permissions to role: {$roleName} for clinic {$clinic->name}");
            } else {
                $role->syncPermissions($perms);
                $this->command->info('Assigned '.count($perms)." permissions to role: {$roleName} for clinic {$clinic->name}");
            }
        }
    }
}
