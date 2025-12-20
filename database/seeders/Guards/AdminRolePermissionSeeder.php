<?php

namespace Database\Seeders\Guards;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Admin;

class AdminRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $teamId = Admin::TeamId;

        // Set the team context for permissions
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($teamId);
        }

        $this->command->info("Creating permissions for admin guard with team ID {$teamId}");

        // Define all admin permissions grouped
        $permissionGroups = [
            'Dashboard' => ['view dashboard'],
            'Contact Messages' => ['view contact messages','view trash contact messages', 'update contact message', 'delete contact message', 'update contact message status', 'add contact message notes'],
            'Users' => ['view users','view trash users', 'create user', 'update user', 'delete user', 'toggle user status', 'change user password'],
            "System Management"=>['view system management' , 'view system clinics' , 'view system clinic users' , 'view system doctor profiles' , 'view system suppliers' ,'view system supplier users'],
            'Clinic Management'=>['view clinics','view trash clinics' , 'view clinic users' , 'view clinic doctor profiles' , 'create clinic', 'update clinic', 'delete clinic', 'approve clinic', 'reject clinic', 'toggle clinic status', 'toggle clinic allowed'],
            'Specialities' => ['view specialities','view trash specialities', 'create speciality', 'update speciality', 'delete speciality'],
            'Orders' => ['view orders','view trash orders', 'update order', 'update order payment status'],
            'Rental Spaces' => ['view rental spaces','view trash rental spaces', 'create rental space', 'update rental space', 'delete rental space', 'restore rental space', 'force delete rental space', 'toggle rental space status'],
            'Patients' => ['view patients','view trash patients', 'create patient', 'update patient', 'delete patient', 'restore patient', 'force delete patient', 'toggle patient status'],
            'Suppliers' => ['view suppliers','view trash suppliers', 'create supplier', 'update supplier', 'delete supplier', 'approve supplier', 'reject supplier', 'toggle supplier status', 'toggle supplier allowed'],
            'Supplier Products' => ['view supplier products','view trash supplier products', 'approve supplier product', 'reject supplier product'],
            'Categories' => ['view categories','view trash categories', 'create category', 'update category', 'delete category', 'restore category', 'force delete category', 'toggle category status'],
            'Announcements' => ['view announcements','view trash announcements', 'create announcement', 'update announcement', 'delete announcement'],
            'Translations' => ['view translations','view trash translations', 'create translation', 'update translation', 'delete translation'],
            'Admin Users' => ['view admin users','view trash admin users', 'create admin user', 'update admin user', 'delete admin user', 'restore admin user', 'force delete admin user', 'toggle admin user status'],
            'Roles' => ['view roles','view trash roles', 'create role', 'update role', 'delete role', 'restore role', 'force delete role'],
            'Settings' => ['view settings', 'update settings'],
            'Module Approvements' => ['view approvements', 'update approvement'],
            'Blog Categories' => ['view blog categories','view trash blog categories', 'create blog category', 'update blog category', 'delete blog category', 'restore blog category', 'force delete blog category', 'toggle blog category status'],
            'Blog Posts' => ['view blog posts','view trash blog posts', 'create blog post', 'update blog post', 'delete blog post', 'restore blog post', 'force delete blog post', 'toggle blog post status'],
            'Courses' => ['view courses','view trash courses', 'create course', 'update course', 'delete course', 'restore course', 'force delete course', 'toggle course status'],
            'Course Enrollments' => ['view course enrollments','view trash course enrollments', 'update course enrollment', 'delete course enrollment'],
            'Jobs' => ['view jobs','view trash jobs', 'create job', 'update job', 'delete job', 'restore job', 'force delete job', 'toggle job status'],
            'Purchase Requests' => ['view purchase requests','view trash purchase requests'],
            'Tickets' => ['view tickets','view trash tickets', 'update ticket', 'delete ticket', 'restore ticket', 'force delete ticket', 'reply ticket', 'update ticket status'],
            'Locations' => [
                'view governorates','view trash governorates', 'create governorate', 'update governorate', 'delete governorate', 'restore governorate', 'force delete governorate',
                'view cities','view trash cities', 'create city', 'update city', 'delete city', 'restore city', 'force delete city',
                'view areas','view trash areas', 'create area', 'update area', 'delete area', 'restore area', 'force delete area',
            ],
            'Subscriptions' => [
                'view plans','view trash plans', 'create plan', 'update plan', 'delete plan', 'manage plan features',
                'view features','view trash features', 'create feature', 'update feature', 'delete feature',
                'view subscriptions','view trash subscriptions', 'create subscription', 'update subscription', 'delete subscription', 'extend subscription', 'cancel subscription',
            ],
            'Affiliates' => [
                'view affiliates', 'update affiliates',
                'view affiliate settings', 'update affiliate settings',
            ],
            'Doctor Profiles' => ['view doctor profiles', 'approve doctor profile', 'reject doctor profile', 'toggle featured doctor profile', 'toggle lock doctor profile'],
            'Notifications' => ['view notifications','view trash notifications', 'mark notification as read', 'mark all notifications as read'],
        ];

        $permissions = [];
        foreach ($permissionGroups as $group => $perms) {
            $permissions = array_merge($permissions, $perms);
        }

        // Create permissions with groups
        foreach ($permissionGroups as $group => $perms) {
            foreach ($perms as $permission) {
                Permission::updateOrCreate(
                    [
                        'name' => $permission,
                        'guard_name' => 'admin'
                    ],
                    [
                        'group' => $group,
                    ]
                );
            }
        }

        $this->command->info("Creating roles for admin guard");

        // Define roles with their permissions
        $roles = [
            'super-admin' => 'all', // All permissions
            'admin' => [
                'view dashboard',
                'view users', 'create user', 'update user', 'delete user', 'toggle user status',
                'view roles', 'create role', 'update role', 'delete role',
                'view settings', 'update settings',
                'view categories', 'create category', 'update category', 'delete category', 'toggle category status',
                'view specialities', 'create speciality', 'update speciality', 'delete speciality',
                'view announcements', 'create announcement', 'update announcement', 'delete announcement',
                'view clinics', 'create clinic', 'update clinic', 'approve clinic', 'reject clinic', 'toggle clinic status', 'toggle clinic allowed',
                'view suppliers', 'create supplier', 'update supplier', 'approve supplier', 'reject supplier', 'toggle supplier status', 'toggle supplier allowed',
                'view supplier products', 'approve supplier product', 'reject supplier product',
                'view rental spaces', 'create rental space', 'update rental space', 'delete rental space', 'toggle rental space status',
                'view approvements', 'update approvement',
                'view blog categories', 'create blog category', 'update blog category', 'delete blog category', 'toggle blog category status',
                'view blog posts', 'create blog post', 'update blog post', 'delete blog post', 'toggle blog post status',
                'view courses', 'create course', 'update course', 'delete course', 'toggle course status',
                'view jobs', 'create job', 'update job', 'delete job', 'toggle job status',
                'view orders', 'update order', 'update order payment status',
                'view purchase requests',
                'view tickets', 'update ticket', 'delete ticket', 'reply ticket', 'update ticket status',
                'view governorates', 'create governorate', 'update governorate', 'delete governorate',
                'view cities', 'create city', 'update city', 'delete city',
                'view areas', 'create area', 'update area', 'delete area',
                'view course enrollments', 'update course enrollment', 'delete course enrollment',
                'view plans', 'create plan', 'update plan', 'delete plan', 'manage plan features',
                'view features', 'create feature', 'update feature', 'delete feature',
                'view subscriptions', 'create subscription', 'update subscription', 'delete subscription', 'extend subscription', 'cancel subscription',
                'view affiliates', 'update affiliates', 'view affiliate settings', 'update affiliate settings',
                'view doctor profiles', 'approve doctor profile', 'reject doctor profile', 'toggle featured doctor profile', 'toggle lock doctor profile',
                'view contact messages', 'update contact message', 'delete contact message', 'update contact message status', 'add contact message notes',
                'view admin users', 'create admin user', 'update admin user', 'delete admin user', 'toggle admin user status',
                'view notifications',
            ],
            'moderator' => [
                'view dashboard',
                'view users',
                'view roles',
                'view categories', 'update category', 'toggle category status',
                'view specialities', 'update speciality',
                'view announcements', 'create announcement', 'update announcement',
                'view clinics', 'approve clinic', 'reject clinic',
                'view suppliers', 'approve supplier', 'reject supplier',
                'view supplier products', 'approve supplier product', 'reject supplier product',
                'view rental spaces', 'update rental space', 'toggle rental space status',
                'view blog categories', 'update blog category', 'toggle blog category status',
                'view blog posts', 'update blog post', 'toggle blog post status',
                'view courses', 'update course', 'toggle course status',
                'view jobs', 'update job', 'toggle job status',
                'view orders',
                'view tickets', 'reply ticket', 'update ticket status',
                'view affiliates', 'view affiliate settings',
                'view doctor profiles', 'approve doctor profile', 'reject doctor profile',
                'view contact messages', 'update contact message', 'update contact message status', 'add contact message notes',
                'view notifications',
            ],
            'viewer' => [
                'view dashboard',
                'view users',
                'view roles',
                'view categories',
                'view specialities',
                'view announcements',
                'view clinics',
                'view suppliers',
                'view supplier products',
                'view rental spaces',
                'view blog categories',
                'view blog posts',
                'view courses',
                'view jobs',
                'view orders',
                'view tickets',
                'view doctor profiles',
                'view contact messages',
                'view notifications',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'admin',
                'team_id' => $teamId
            ]);

            if ($perms === 'all') {
                $allPermissions = Permission::where('guard_name', 'admin')->get();
                $role->syncPermissions($allPermissions);
                $this->command->info("Assigned all permissions to role: {$roleName}");
            } else {
                $role->syncPermissions($perms);
                $this->command->info("Assigned " . count($perms) . " permissions to role: {$roleName}");
            }
        }

        $this->command->info('Admin roles and permissions seeded successfully!');
    }
}









