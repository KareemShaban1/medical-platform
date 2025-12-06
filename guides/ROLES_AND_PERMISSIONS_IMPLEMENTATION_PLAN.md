# Roles and Permissions Implementation Plan
## Multi-Auth Medical Platform

---

## 📋 Table of Contents
1. [Current State Analysis](#current-state-analysis)
2. [Architecture Overview](#architecture-overview)
3. [Permission Naming Conventions](#permission-naming-conventions)
4. [Implementation Strategy by Dashboard](#implementation-strategy-by-dashboard)
5. [Middleware Strategy](#middleware-strategy)
6. [Policy Strategy](#policy-strategy)
7. [Database & Seeder Updates](#database--seeder-updates)
8. [View/UI Updates](#viewui-updates)
9. [Testing Strategy](#testing-strategy)
10. [Migration & Rollout Plan](#migration--rollout-plan)

---

## 🔍 Current State Analysis

### Existing Infrastructure
- ✅ **Spatie Permission Package**: Installed and configured
- ✅ **Multi-Guard Setup**: 5 guards configured (admin, clinic, supplier, patient, web)
- ✅ **Models with HasRoles Trait**: 
  - `Admin` (guard: admin)
  - `ClinicUser` (guard: clinic)
  - `SupplierUser` (guard: supplier)
- ✅ **Role & Permission Seeder**: Basic structure exists
- ✅ **Policies**: 21 policies exist but may not be actively used
- ✅ **Role Management UI**: Exists for admin, clinic, and supplier dashboards

### Current Gaps
- ❌ **No Permission Checks**: Controllers don't check permissions
- ❌ **Policies Not Registered**: Policies exist but not registered in AuthServiceProvider
- ❌ **Incomplete Permissions**: Seeder has basic permissions, needs expansion
- ❌ **No Permission Middleware**: No middleware for route-level permission checks
- ❌ **UI Not Permission-Aware**: Views don't hide/show based on permissions
- ❌ **Patient Dashboard**: No role/permission system (may not be needed)

---

## 🏗️ Architecture Overview

### Guard Structure
```
admin    → Admin model        → Team-based (TeamId = 1)
clinic   → ClinicUser model   → Team-based (team_id = clinic_id)
supplier → SupplierUser model → Team-based (team_id = supplier_id)
patient  → Patient model      → No roles/permissions (simple user)
```

### Permission Hierarchy
```
Permission → Role → User
```

### Team-Based Permissions
- **Admin**: Fixed team ID (1)
- **Clinic**: Each clinic has isolated permissions (team_id = clinic_id)
- **Supplier**: Each supplier has isolated permissions (team_id = supplier_id)

---

## 📝 Permission Naming Conventions

### Format: `{action} {resource}`

### Actions (Standard CRUD + Custom)
- `view` - Read/list resources
- `create` - Create new resource
- `update` - Edit existing resource
- `delete` - Soft delete resource
- `restore` - Restore soft-deleted resource
- `force-delete` - Permanently delete
- `approve` - Approve resource
- `reject` - Reject resource
- `toggle-status` - Enable/disable resource
- `export` - Export data
- `import` - Import data

### Resources (Entity Names)
- Use singular form: `user`, `role`, `patient`, `appointment`, etc.
- Use descriptive names: `doctor-profile`, `clinic-inventory`, `purchase-request`

### Examples
```
view users
create user
update user
delete user
view roles
create role
update role
delete role
view patients
create patient
update patient
delete patient
view appointments
create appointment
update appointment
delete appointment
approve doctor profile
reject doctor profile
```

---

## 🎯 Implementation Strategy by Dashboard

### 1. Admin Dashboard (`admin.*`)

#### Controllers to Secure (27 controllers)
1. `DashboardController` - `view dashboard`
2. `UsersManagementController` - `view users`, `update user`, `delete user`
3. `CategoryController` - `view categories`, `create category`, `update category`, `delete category`
4. `SpecialityController` - `view specialities`, `create speciality`, `update speciality`, `delete speciality`
5. `RoleController` - `view roles`, `create role`, `update role`, `delete role`
6. `AnnouncementController` - `view announcements`, `create announcement`, `update announcement`, `delete announcement`
7. `ClinicController` - `view clinics`, `create clinic`, `update clinic`, `delete clinic`, `approve clinic`
8. `SupplierController` - `view suppliers`, `create supplier`, `update supplier`, `delete supplier`, `approve supplier`
9. `SupplierProductController` - `view supplier products`, `approve supplier product`
10. `RentalSpaceController` - `view rental spaces`, `create rental space`, `update rental space`, `delete rental space`
11. `ModuleApprovementController` - `view approvements`, `update approvement`
12. `BlogCategoryController` - `view blog categories`, `create blog category`, `update blog category`, `delete blog category`
13. `BlogPostController` - `view blog posts`, `create blog post`, `update blog post`, `delete blog post`
14. `CourseController` - `view courses`, `create course`, `update course`, `delete course`
15. `JobController` - `view jobs`, `create job`, `update job`, `delete job`
16. `OrderController` - `view orders`, `update order`
17. `PurchaseRequestController` - `view purchase requests`
18. `TicketController` - `view tickets`, `update ticket`, `delete ticket`, `reply ticket`
19. `GovernorateController` - `view governorates`, `create governorate`, `update governorate`, `delete governorate`
20. `CityController` - `view cities`, `create city`, `update city`, `delete city`
21. `AreaController` - `view areas`, `create area`, `update area`, `delete area`
22. `CourseEnrollmentController` - `view course enrollments`, `update course enrollment`, `delete course enrollment`
23. `PlanController` - `view plans`, `create plan`, `update plan`, `delete plan`
24. `FeatureMasterController` - `view features`, `create feature`, `update feature`, `delete feature`
25. `SubscriptionManagementController` - `view subscriptions`, `create subscription`, `update subscription`, `delete subscription`
26. `DoctorProfileController` - `view doctor profiles`, `approve doctor profile`, `reject doctor profile`, `toggle featured doctor profile`
27. `ContactMessageController` - `view contact messages`, `update contact message`, `delete contact message`
28. `AdminUserController` - `view admin users`, `create admin user`, `update admin user`, `delete admin user`
29. `NotificationController` - `view notifications`

#### Permissions List (Admin Guard)
```php
// Dashboard
'view dashboard'

// Users Management
'view users', 'create user', 'update user', 'delete user', 'toggle user status', 'change user password'

// Roles Management
'view roles', 'create role', 'update role', 'delete role', 'restore role', 'force delete role'

// Settings
'view settings', 'update settings'

// Categories
'view categories', 'create category', 'update category', 'delete category', 'restore category', 'force delete category', 'toggle category status'

// Specialities
'view specialities', 'create speciality', 'update speciality', 'delete speciality'

// Announcements
'view announcements', 'create announcement', 'update announcement', 'delete announcement'

// Clinics Management
'view clinics', 'create clinic', 'update clinic', 'delete clinic', 'approve clinic', 'reject clinic', 'toggle clinic status', 'toggle clinic allowed'

// Suppliers Management
'view suppliers', 'create supplier', 'update supplier', 'delete supplier', 'approve supplier', 'reject supplier', 'toggle supplier status', 'toggle supplier allowed'

// Supplier Products
'view supplier products', 'approve supplier product', 'reject supplier product'

// Rental Spaces
'view rental spaces', 'create rental space', 'update rental space', 'delete rental space', 'restore rental space', 'force delete rental space', 'toggle rental space status'

// Module Approvements
'view approvements', 'update approvement'

// Blog Categories
'view blog categories', 'create blog category', 'update blog category', 'delete blog category', 'restore blog category', 'force delete blog category', 'toggle blog category status'

// Blog Posts
'view blog posts', 'create blog post', 'update blog post', 'delete blog post', 'restore blog post', 'force delete blog post', 'toggle blog post status'

// Courses
'view courses', 'create course', 'update course', 'delete course', 'restore course', 'force delete course', 'toggle course status'

// Jobs
'view jobs', 'create job', 'update job', 'delete job', 'restore job', 'force delete job', 'toggle job status'

// Orders
'view orders', 'update order', 'update order payment status'

// Purchase Requests
'view purchase requests'

// Tickets
'view tickets', 'update ticket', 'delete ticket', 'restore ticket', 'force delete ticket', 'reply ticket', 'update ticket status'

// Locations
'view governorates', 'create governorate', 'update governorate', 'delete governorate', 'restore governorate', 'force delete governorate'
'view cities', 'create city', 'update city', 'delete city', 'restore city', 'force delete city'
'view areas', 'create area', 'update area', 'delete area', 'restore area', 'force delete area'

// Course Enrollments
'view course enrollments', 'update course enrollment', 'delete course enrollment'

// Subscription Management
'view plans', 'create plan', 'update plan', 'delete plan', 'manage plan features'
'view features', 'create feature', 'update feature', 'delete feature'
'view subscriptions', 'create subscription', 'update subscription', 'delete subscription', 'extend subscription', 'cancel subscription'

// Doctor Profiles
'view doctor profiles', 'approve doctor profile', 'reject doctor profile', 'toggle featured doctor profile', 'toggle lock doctor profile'

// Contact Messages
'view contact messages', 'update contact message', 'delete contact message', 'update contact message status', 'add contact message notes'

// Admin Users
'view admin users', 'create admin user', 'update admin user', 'delete admin user', 'restore admin user', 'force delete admin user', 'toggle admin user status'

// Notifications
'view notifications'
```

#### Default Roles (Admin)
- **super-admin**: All permissions
- **admin**: Most permissions except sensitive operations
- **moderator**: View and moderate content
- **viewer**: Read-only access

---

### 2. Clinic Dashboard (`clinic.*`)

#### Controllers to Secure (30+ controllers)
1. `DashboardController` - `view dashboard`
2. `UserController` - `view users`, `create user`, `update user`, `delete user`
3. `RoleController` - `view roles`, `create role`, `update role`, `delete role`
4. `PatientController` - `view patients`, `create patient`, `update patient`, `delete patient`
5. `AppointmentController` - `view appointments`, `create appointment`, `update appointment`, `delete appointment`, `confirm appointment`, `cancel appointment`
6. `DoctorProfileController` - `view doctor profiles`, `create doctor profile`, `update doctor profile`, `delete doctor profile`, `submit doctor profile`
7. `JobController` - `view jobs`, `create job`, `update job`, `delete job`
8. `RequestController` - `view purchase requests`, `create purchase request`, `update purchase request`, `delete purchase request`, `accept offer`, `cancel request`
9. `ClinicInventoryController` - `view clinic inventories`, `create clinic inventory`, `update clinic inventory`, `delete clinic inventory`
10. `ClinicInventoryMovementsController` - `view clinic inventory movements`, `create clinic inventory movement`, `update clinic inventory movement`, `delete clinic inventory movement`
11. `ClinicUserSalaryController` - `view clinic user salaries`, `create clinic user salary`, `update clinic user salary`, `delete clinic user salary`
12. `SalaryContractController` - `view salary contracts`, `create salary contract`, `update salary contract`, `delete salary contract`
13. `PayslipController` - `view payslips`, `create payslip`, `update payslip`, `delete payslip`
14. `ExpenseCategoryController` - `view expense categories`, `create expense category`, `update expense category`, `delete expense category`
15. `ExpenseController` - `view expenses`, `create expense`, `update expense`, `delete expense`
16. `PrescriptionController` - `view prescriptions`, `create prescription`, `update prescription`, `delete prescription`, `print prescription`
17. `MedicalRecordController` - `view medical records`, `update medical record`, `share medical record`
18. `LabOrderController` - `view lab orders`, `create lab order`, `update lab order`, `upload lab order`, `complete lab order`
19. `InvoiceController` - `view invoices`, `create invoice`, `update invoice`, `mark invoice paid`
20. `RentalSpaceController` - `view rental spaces`, `create rental space`, `update rental space`, `delete rental space`
21. `AttendanceController` - `view attendance`, `check in`, `check out`, `mark absence`, `approve attendance`
22. `WorkingHourController` - `view working hours`, `create working hour`, `update working hour`, `delete working hour`
23. `AvailabilityOverrideController` - `view availability overrides`, `create availability override`, `update availability override`, `delete availability override`
24. `DailyPeriodController` - `view daily periods`, `create daily period`, `update daily period`, `delete daily period`, `generate daily periods`
25. `SubscriptionController` - `view subscriptions`, `subscribe`, `cancel subscription`
26. `ClinicInfoController` - `view clinic info`, `update clinic info`
27. `NotificationController` - `view notifications`
28. `AnnouncementController` - `dismiss announcement`
29. `OrderController` - `view orders`
30. `CourseEnrollmentController` - `view course enrollments`
31. `JobApplicationFieldController` - `view job application fields`, `create job application field`, `update job application field`, `delete job application field`
32. `ApprovalController` - `view approval`, `upload approval documents`

#### Permissions List (Clinic Guard)
```php
// Dashboard
'view dashboard'

// Users Management
'view users', 'create user', 'update user', 'delete user', 'restore user', 'force delete user', 'toggle user status'

// Roles Management
'view roles', 'create role', 'update role', 'delete role', 'restore role', 'force delete role'

// Settings
'view settings', 'update settings', 'view clinic info', 'update clinic info'

// Patients
'view patients', 'create patient', 'update patient', 'delete patient', 'restore patient', 'force delete patient'

// Appointments
'view appointments', 'create appointment', 'update appointment', 'delete appointment', 'restore appointment', 'force delete appointment', 'confirm appointment', 'cancel appointment'

// Doctor Profiles
'view doctor profiles', 'create doctor profile', 'update doctor profile', 'delete doctor profile', 'restore doctor profile', 'force delete doctor profile', 'submit doctor profile'

// Jobs
'view jobs', 'create job', 'update job', 'delete job', 'restore job', 'force delete job', 'toggle job status', 'view job applicants', 'update job application status', 'update job application data'

// Purchase Requests
'view purchase requests', 'create purchase request', 'update purchase request', 'delete purchase request', 'accept offer', 'cancel request', 'process offer payment'

// Clinic Inventory
'view clinic inventories', 'create clinic inventory', 'update clinic inventory', 'delete clinic inventory', 'restore clinic inventory', 'force delete clinic inventory'
'view clinic inventory movements', 'create clinic inventory movement', 'update clinic inventory movement', 'delete clinic inventory movement', 'restore clinic inventory movement', 'force delete clinic inventory movement'

// Salaries & Contracts
'view clinic user salaries', 'create clinic user salary', 'update clinic user salary', 'delete clinic user salary', 'restore clinic user salary', 'force delete clinic user salary'
'view salary contracts', 'create salary contract', 'update salary contract', 'delete salary contract', 'restore salary contract', 'force delete salary contract'
'view payslips', 'create payslip', 'update payslip', 'delete payslip', 'restore payslip', 'force delete payslip'

// Expenses
'view expense categories', 'create expense category', 'update expense category', 'delete expense category', 'restore expense category', 'force delete expense category', 'toggle expense category status'
'view expenses', 'create expense', 'update expense', 'delete expense', 'restore expense', 'force delete expense'

// Prescriptions
'view prescriptions', 'create prescription', 'update prescription', 'delete prescription', 'print prescription', 'download prescription'

// Medical Records
'view medical records', 'update medical record', 'share medical record'

// Lab Orders
'view lab orders', 'create lab order', 'update lab order', 'upload lab order', 'complete lab order'

// Invoices
'view invoices', 'create invoice', 'update invoice', 'mark invoice paid', 'add invoice item', 'update invoice item', 'delete invoice item', 'update invoice header'

// Rental Spaces
'view rental spaces', 'create rental space', 'update rental space', 'delete rental space', 'restore rental space', 'force delete rental space', 'toggle rental space status'

// Attendance
'view attendance', 'check in', 'check out', 'mark absence', 'approve attendance', 'approve check in', 'approve check out', 'view my attendance logs', 'compute attendance'

// Working Hours
'view working hours', 'create working hour', 'update working hour', 'delete working hour', 'bulk save working hours', 'view user working hours'

// Availability
'view availability overrides', 'create availability override', 'update availability override', 'delete availability override', 'restore availability override', 'force delete availability override', 'view doctor availability overrides'
'view daily periods', 'create daily period', 'update daily period', 'delete daily period', 'view daily period appointments', 'toggle daily period open', 'update daily period capacity', 'generate daily periods'

// Subscriptions
'view subscriptions', 'subscribe', 'cancel subscription', 'view subscription usage'

// Notifications
'view notifications', 'mark notification as read', 'mark all notifications as read'

// Announcements
'dismiss announcement'

// Orders
'view orders'

// Course Enrollments
'view course enrollments'

// Job Application Fields
'view job application fields', 'create job application field', 'update job application field', 'delete job application field', 'toggle job application field status'
```

#### Default Roles (Clinic)
- **clinic-admin**: All permissions for clinic management
- **doctor**: Medical-related permissions (appointments, prescriptions, medical records, doctor profiles)
- **nurse**: Limited medical permissions (view appointments, view patients, view prescriptions)
- **receptionist**: Appointment and patient management
- **accountant**: Financial permissions (salaries, expenses, invoices, payslips)
- **inventory-manager**: Inventory and purchase request permissions
- **hr-manager**: User, role, job, and attendance management
- **staff**: Basic view permissions

---

### 3. Supplier Dashboard (`supplier.*`)

#### Controllers to Secure (13 controllers)
1. `DashboardController` - `view dashboard`
2. `UserController` - `view users`, `create user`, `update user`, `delete user`
3. `RoleController` - `view roles`, `create role`, `update role`, `delete role`
4. `ProductController` - `view products`, `create product`, `update product`, `delete product`, `toggle product status`
5. `OfferController` - `view offers`, `create offer`, `update offer`, `delete offer`, `view available requests`
6. `OrderController` - `view orders`, `update order status`, `update order payment status`, `create refund`, `update refund status`
7. `SpecializedCategoryController` - `view specialized categories`, `create specialized category`, `update specialized category`, `delete specialized category`, `attach specialized category`
8. `SubscriptionController` - `view subscriptions`, `subscribe`, `cancel subscription`
9. `SupplierInfoController` - `view supplier info`, `update supplier info`
10. `NotificationController` - `view notifications`
11. `AnnouncementController` - `dismiss announcement`
12. `ApprovalController` - `view approval`, `upload approval documents`

#### Permissions List (Supplier Guard)
```php
// Dashboard
'view dashboard'

// Users Management
'view users', 'create user', 'update user', 'delete user', 'restore user', 'force delete user', 'toggle user status'

// Roles Management
'view roles', 'create role', 'update role', 'delete role', 'restore role', 'force delete role'

// Settings
'view settings', 'update settings', 'view supplier info', 'update supplier info'

// Products
'view products', 'create product', 'update product', 'delete product', 'restore product', 'force delete product', 'toggle product status'

// Offers
'view offers', 'create offer', 'update offer', 'delete offer'
'view available requests', 'create offer for request'

// Orders
'view orders', 'update order status', 'update order payment status', 'create refund', 'update refund status', 'view order analytics'

// Specialized Categories
'view specialized categories', 'create specialized category', 'update specialized category', 'delete specialized category', 'attach specialized category', 'view available categories'

// Subscriptions
'view subscriptions', 'subscribe', 'cancel subscription', 'view subscription usage'

// Notifications
'view notifications', 'mark notification as read', 'mark all notifications as read'

// Announcements
'dismiss announcement'
```

#### Default Roles (Supplier)
- **supplier-admin**: All permissions
- **supplier-manager**: Product and order management
- **supplier-staff**: View products and orders, limited update permissions
- **supplier-viewer**: Read-only access

---

### 4. Patient Dashboard (`user.*`)

#### Decision: No Roles/Permissions Needed
- Patient dashboard is simple and user-specific
- Each patient only accesses their own data
- No multi-user management needed
- Keep as-is with simple authentication

---

### 5. Doctor Dashboard (`doctor.*`)

#### Decision: Use Clinic Guard
- Doctors use `ClinicUser` model with `clinic_id = null`
- Already part of clinic guard
- Use clinic permissions with doctor role
- No separate implementation needed

---

## 🛡️ Middleware Strategy

### 1. Create Permission Middleware

**File**: `app/Http/Middleware/CheckPermission.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $guard = $this->detectGuard();
        
        if (!$guard || !Auth::guard($guard)->check()) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::guard($guard)->user();

        if (!$user->can($permission)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('You do not have permission to perform this action.'),
                    'permission' => $permission
                ], 403);
            }

            abort(403, __('You do not have permission to perform this action.'));
        }

        return $next($request);
    }

    protected function detectGuard(): ?string
    {
        $route = request()->route();
        $prefix = $route->getPrefix() ?? '';

        if (str_contains($prefix, '/admin')) {
            return 'admin';
        } elseif (str_contains($prefix, '/clinic')) {
            return 'clinic';
        } elseif (str_contains($prefix, '/supplier')) {
            return 'supplier';
        } elseif (str_contains($prefix, '/user')) {
            return 'patient';
        }

        return null;
    }
}
```

### 2. Register Middleware

**File**: `bootstrap/app.php`

```php
$middleware->alias([
    // ... existing aliases
    'permission' => \App\Http\Middleware\CheckPermission::class,
]);
```

### 3. Usage in Routes

```php
// Single permission
Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:view users')
    ->name('users.index');

// Multiple permissions (any)
Route::post('/users', [UserController::class, 'store'])
    ->middleware('permission:create user')
    ->name('users.store');
```

---

## 📋 Policy Strategy

### 1. Register All Policies

**File**: `app/Providers/AuthServiceProvider.php` (Create if doesn't exist)

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Admin Policies
        \App\Models\Admin::class => \App\Policies\AdminPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\BlogCategory::class => \App\Policies\BlogCategoryPolicy::class,
        \App\Models\BlogPost::class => \App\Policies\BlogPostPolicy::class,
        \App\Models\Course::class => \App\Policies\CoursePolicy::class,
        \App\Models\Clinic::class => \App\Policies\ClinicPolicy::class,
        \App\Models\Supplier::class => \App\Policies\SupplierPolicy::class,
        \App\Models\Governorate::class => \App\Policies\GovernoratePolicy::class,
        \App\Models\City::class => \App\Policies\CityPolicy::class,
        \App\Models\Area::class => \App\Policies\AreaPolicy::class,
        
        // Clinic Policies
        \App\Models\ClinicUser::class => \App\Policies\ClinicUserPolicy::class,
        \App\Models\Patient::class => \App\Policies\PatientPolicy::class, // Create if needed
        \App\Models\Appointment::class => \App\Policies\AppointmentPolicy::class, // Create if needed
        \App\Models\DoctorProfile::class => \App\Policies\DoctorProfilePolicy::class, // Create if needed
        \App\Models\Job::class => \App\Policies\JobPolicy::class,
        \App\Models\ClinicInventory::class => \App\Policies\ClinicInventoryPolicy::class,
        \App\Models\ClinicUserSalary::class => \App\Policies\ClinicUserSalaryPolicy::class,
        \App\Models\SalaryContract::class => \App\Policies\SalaryContractPolicy::class,
        \App\Models\Payslip::class => \App\Policies\PayslipPolicy::class,
        \App\Models\ExpenseCategory::class => \App\Policies\ExpenseCategoryPolicy::class,
        \App\Models\Expense::class => \App\Policies\ExpensePolicy::class,
        \App\Models\Prescription::class => \App\Policies\PrescriptionPolicy::class,
        \App\Models\JobApplicationField::class => \App\Policies\JobApplicationFieldsPolicy::class,
        
        // Supplier Policies
        \App\Models\SupplierUser::class => \App\Policies\SupplierUserPolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class, // Create if needed
        \App\Models\Offer::class => \App\Policies\OfferPolicy::class, // Create if needed
        \App\Models\Order::class => \App\Policies\OrderPolicy::class, // Create if needed
    ];

    public function boot(): void
    {
        // Register policies
    }
}
```

### 2. Update Existing Policies

Ensure all policies check permissions:

```php
public function viewAny(User $user): bool
{
    return $user->can('view users');
}

public function create(User $user): bool
{
    return $user->can('create user');
}

public function update(User $user, Model $model): bool
{
    return $user->can('update user');
}

public function delete(User $user, Model $model): bool
{
    return $user->can('delete user');
}
```

### 3. Use Policies in Controllers

```php
public function index()
{
    $this->authorize('viewAny', User::class);
    // ... rest of method
}

public function store(Request $request)
{
    $this->authorize('create', User::class);
    // ... rest of method
}

public function update(Request $request, User $user)
{
    $this->authorize('update', $user);
    // ... rest of method
}
```

---

## 🗄️ Database & Seeder Updates

### 1. Update RoleAndPermissionSeeder

**File**: `database/seeders/RoleAndPermissionSeeder.php`

- Expand permissions list for each guard
- Create comprehensive default roles
- Assign permissions to roles
- Handle team-based permissions correctly

### 2. Create Migration for Default Roles

**File**: `database/migrations/XXXX_XX_XX_XXXXXX_assign_default_roles.php`

- Assign default roles to existing users
- Create default roles if they don't exist

---

## 🎨 View/UI Updates

### 1. Blade Directives

Create custom blade directives in `AppServiceProvider`:

```php
Blade::if('hasPermission', function ($permission) {
    $guard = $this->detectGuard();
    if (!$guard || !Auth::guard($guard)->check()) {
        return false;
    }
    return Auth::guard($guard)->user()->can($permission);
});

Blade::if('hasRole', function ($role) {
    $guard = $this->detectGuard();
    if (!$guard || !Auth::guard($guard)->check()) {
        return false;
    }
    return Auth::guard($guard)->user()->hasRole($role);
});
```

### 2. Usage in Views

```blade
@hasPermission('create user')
    <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
@endhasPermission

@hasPermission('delete user')
    <button type="submit" class="btn btn-danger">Delete</button>
@endhasPermission

@hasRole('admin')
    <div class="admin-panel">Admin Content</div>
@endhasRole
```

### 3. Update Navigation Menus

- Hide menu items based on permissions
- Show/hide action buttons
- Disable forms for read-only users

---

## 🧪 Testing Strategy

### 1. Unit Tests
- Test permission checks in controllers
- Test policy methods
- Test middleware

### 2. Feature Tests
- Test role assignment
- Test permission assignment
- Test access control for different roles
- Test team-based permission isolation

### 3. Manual Testing Checklist
- [ ] Admin dashboard permissions
- [ ] Clinic dashboard permissions
- [ ] Supplier dashboard permissions
- [ ] Role isolation (clinic A can't see clinic B's data)
- [ ] Permission inheritance
- [ ] UI visibility based on permissions

---

## 🚀 Migration & Rollout Plan

### Phase 1: Foundation (Week 1)
1. ✅ Create comprehensive permission list
2. ✅ Update `RoleAndPermissionSeeder`
3. ✅ Create `CheckPermission` middleware
4. ✅ Register middleware
5. ✅ Create/update `AuthServiceProvider`
6. ✅ Register all policies

### Phase 2: Admin Dashboard (Week 2)
1. ✅ Add permission checks to Admin controllers
2. ✅ Add permission middleware to Admin routes
3. ✅ Update Admin views with permission checks
4. ✅ Test Admin dashboard

### Phase 3: Clinic Dashboard (Week 3)
1. ✅ Add permission checks to Clinic controllers
2. ✅ Add permission middleware to Clinic routes
3. ✅ Update Clinic views with permission checks
4. ✅ Test Clinic dashboard

### Phase 4: Supplier Dashboard (Week 4)
1. ✅ Add permission checks to Supplier controllers
2. ✅ Add permission middleware to Supplier routes
3. ✅ Update Supplier views with permission checks
4. ✅ Test Supplier dashboard

### Phase 5: UI/UX Polish (Week 5)
1. ✅ Add blade directives
2. ✅ Update all navigation menus
3. ✅ Add permission indicators
4. ✅ Update error messages

### Phase 6: Testing & Documentation (Week 6)
1. ✅ Comprehensive testing
2. ✅ Fix bugs
3. ✅ Update documentation
4. ✅ Create user guide

---

## 📊 Implementation Checklist

### Admin Dashboard
- [ ] DashboardController
- [ ] UsersManagementController
- [ ] CategoryController
- [ ] SpecialityController
- [ ] RoleController
- [ ] AnnouncementController
- [ ] ClinicController
- [ ] SupplierController
- [ ] SupplierProductController
- [ ] RentalSpaceController
- [ ] ModuleApprovementController
- [ ] BlogCategoryController
- [ ] BlogPostController
- [ ] CourseController
- [ ] JobController
- [ ] OrderController
- [ ] PurchaseRequestController
- [ ] TicketController
- [ ] GovernorateController
- [ ] CityController
- [ ] AreaController
- [ ] CourseEnrollmentController
- [ ] PlanController
- [ ] FeatureMasterController
- [ ] SubscriptionManagementController
- [ ] DoctorProfileController
- [ ] ContactMessageController
- [ ] AdminUserController
- [ ] NotificationController

### Clinic Dashboard
- [ ] DashboardController
- [ ] UserController
- [ ] RoleController
- [ ] PatientController
- [ ] AppointmentController
- [ ] DoctorProfileController
- [ ] JobController
- [ ] RequestController
- [ ] ClinicInventoryController
- [ ] ClinicInventoryMovementsController
- [ ] ClinicUserSalaryController
- [ ] SalaryContractController
- [ ] PayslipController
- [ ] ExpenseCategoryController
- [ ] ExpenseController
- [ ] PrescriptionController
- [ ] MedicalRecordController
- [ ] LabOrderController
- [ ] InvoiceController
- [ ] RentalSpaceController
- [ ] AttendanceController
- [ ] WorkingHourController
- [ ] AvailabilityOverrideController
- [ ] DailyPeriodController
- [ ] SubscriptionController
- [ ] ClinicInfoController
- [ ] NotificationController
- [ ] AnnouncementController
- [ ] OrderController
- [ ] CourseEnrollmentController
- [ ] JobApplicationFieldController
- [ ] ApprovalController

### Supplier Dashboard
- [ ] DashboardController
- [ ] UserController
- [ ] RoleController
- [ ] ProductController
- [ ] OfferController
- [ ] OrderController
- [ ] SpecializedCategoryController
- [ ] SubscriptionController
- [ ] SupplierInfoController
- [ ] NotificationController
- [ ] AnnouncementController
- [ ] ApprovalController

---

## 🔐 Security Considerations

1. **Team Isolation**: Ensure clinic/supplier permissions are properly isolated by team_id
2. **Permission Caching**: Clear permission cache after role/permission changes
3. **Default Deny**: Default to denying access if permission check fails
4. **Audit Logging**: Consider logging permission denials for security monitoring
5. **Rate Limiting**: Apply rate limiting to permission-sensitive endpoints

---

## 📚 Additional Notes

- **Backward Compatibility**: Ensure existing functionality continues to work
- **Performance**: Permission checks should be fast (use caching)
- **Documentation**: Document all permissions and their purposes
- **User Training**: Provide training materials for administrators
- **Migration Script**: Create script to assign default roles to existing users

---

## ✅ Next Steps

1. Review this plan with the team
2. Get approval for the approach
3. Start with Phase 1 (Foundation)
4. Implement incrementally
5. Test thoroughly at each phase
6. Deploy to staging first
7. Monitor and adjust as needed

---

**Document Version**: 1.0  
**Last Updated**: 2024  
**Author**: AI Assistant  
**Status**: Draft - Pending Review











