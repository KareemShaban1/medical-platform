<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\Supplier;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionService
{
    /**
     * Create roles and permissions for a newly registered clinic.
     *
     * @param Clinic $clinic
     * @return Role The clinic-admin role
     */
    public function createClinicRolesAndPermissions(Clinic $clinic): Role
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $teamId = $clinic->id;

        // Set the team context for permissions
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($teamId);
        }

        // Ensure all clinic permissions exist
        $this->ensureClinicPermissionsExist();

        // Create clinic-admin role with all permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'clinic-admin',
            'guard_name' => 'clinic',
            'team_id' => $teamId,
        ]);

        // Assign all clinic permissions to admin role
        $allPermissions = Permission::where('guard_name', 'clinic')->get();
        $adminRole->syncPermissions($allPermissions);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return $adminRole;
    }

    /**
     * Create roles and permissions for a newly registered supplier.
     *
     * @param Supplier $supplier
     * @return Role The supplier-admin role
     */
    public function createSupplierRolesAndPermissions(Supplier $supplier): Role
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $teamId = $supplier->id;

        // Set the team context for permissions
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($teamId);
        }

        // Ensure all supplier permissions exist
        $this->ensureSupplierPermissionsExist();

        // Create supplier-admin role with all permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'supplier-admin',
            'guard_name' => 'supplier',
            'team_id' => $teamId,
        ]);

        // Assign all supplier permissions to admin role
        $allPermissions = Permission::where('guard_name', 'supplier')->get();
        $adminRole->syncPermissions($allPermissions);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return $adminRole;
    }

    /**
     * Ensure all clinic guard permissions exist.
     */
    protected function ensureClinicPermissionsExist(): void
    {
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

        foreach ($permissionGroups as $group => $perms) {
            foreach ($perms as $permission) {
                Permission::firstOrCreate(
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
    }

    /**
     * Ensure all supplier guard permissions exist.
     */
    protected function ensureSupplierPermissionsExist(): void
    {
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

        foreach ($permissionGroups as $group => $perms) {
            foreach ($perms as $permission) {
                Permission::firstOrCreate(
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
    }
}