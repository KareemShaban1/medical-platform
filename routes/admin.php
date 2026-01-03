<?php

use App\Http\Controllers\Backend\Dashboards\Admin\AnnouncementController;
use App\Http\Controllers\Backend\Dashboards\Admin\BannerController;
use App\Http\Controllers\Backend\Dashboards\Admin\BlogCategoryController;
use App\Http\Controllers\Backend\Dashboards\Admin\ContactMessageController;
use App\Http\Controllers\Backend\Dashboards\Admin\DashboardController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Dashboards\Admin\CategoryController;
use App\Http\Controllers\Backend\Dashboards\Admin\ClinicController;
use App\Http\Controllers\Backend\Dashboards\Admin\SupplierController;
use App\Http\Controllers\Backend\Dashboards\Admin\SupplierProductController;
use App\Http\Controllers\Backend\Dashboards\Admin\RentalSpaceController;
use App\Http\Controllers\Backend\Dashboards\Admin\ModuleApprovementController;
use App\Http\Controllers\Backend\Dashboards\Admin\BlogPostController;
use App\Http\Controllers\Backend\Dashboards\Admin\CourseController;
use App\Http\Controllers\Backend\Dashboards\Admin\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Backend\Dashboards\Admin\SpecialityController;


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/admin',
        'as' => 'admin.',
        'namespace' => 'App\Http\Controllers\Backend\Dashboards\Admin',
        'middleware' => [
            'auth:admin',
            'verified',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Contact Messages
        Route::group(['prefix' => 'contact-messages', 'as' => 'contact-messages.'], function () {
            Route::get('/', [ContactMessageController::class, 'index'])->name('index');
            Route::get('/data', [ContactMessageController::class, 'data'])->name('data');
            Route::get('/{id}', [ContactMessageController::class, 'show'])->name('show');
            Route::post('/{id}/update-status', [ContactMessageController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/add-notes', [ContactMessageController::class, 'addNotes'])->name('add-notes');
            Route::delete('/{id}', [ContactMessageController::class, 'destroy'])->name('destroy');
        });

        // Users Management
        Route::group(['prefix' => 'users-management', 'as' => 'users-management.'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'index'])->name('index');

            // Clinics
            Route::get('/clinics', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'clinics'])->name('clinics');
            Route::get('/clinics/data', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'clinicsData'])->name('clinics.data');
            Route::get('/clinics/{id}/details', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'clinicDetails'])->name('clinic-details');

            // Clinic Users
            Route::get('/clinic-users', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'clinicUsers'])->name('clinic-users');
            Route::get('/clinic-users/data', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'clinicUsersData'])->name('clinic-users.data');
            Route::get('/clinic-users/{id}/details', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'clinicUserDetails'])->name('clinic-user-details');

            // Patients
            Route::get('/patients', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'patients'])->name('patients');
            Route::get('/patients/data', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'patientsData'])->name('patients.data');
            Route::get('/patients/{id}/details', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'patientDetails'])->name('patient-details');

            // Doctor Profiles
            Route::get('/doctor-profiles', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'doctorProfiles'])->name('doctor-profiles');
            Route::get('/doctor-profiles/data', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'doctorProfilesData'])->name('doctor-profiles.data');
            Route::get('/doctor-profiles/{id}/details', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'doctorProfileDetails'])->name('doctor-profile-details');

            // Suppliers
            Route::get('/suppliers', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'suppliers'])->name('suppliers');
            Route::get('/suppliers/data', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'suppliersData'])->name('suppliers.data');
            Route::get('/suppliers/{id}/details', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'supplierDetails'])->name('supplier-details');

            // Supplier Users
            Route::get('/supplier-users', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'supplierUsers'])->name('supplier-users');
            Route::get('/supplier-users/data', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'supplierUsersData'])->name('supplier-users.data');
            Route::get('/supplier-users/{id}/details', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'supplierUserDetails'])->name('supplier-user-details');

            // User Management Actions
            Route::post('/change-password', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'changePassword'])->name('change-password');
            Route::post('/toggle-status', [\App\Http\Controllers\Backend\Dashboards\Admin\UsersManagementController::class, 'toggleStatus'])->name('toggle-status');
        });

        Route::get('categories/data', [CategoryController::class, 'data'])->name('categories.data');
        Route::put('categories/{id}/update-status', [CategoryController::class, 'updateStatus'])->name('categories.update-status');
        Route::resource('categories', CategoryController::class);

        // Doctor Specialities
        Route::get('specialities/data', [SpecialityController::class, 'data'])->name('specialities.data');
        Route::resource('specialities', SpecialityController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

        // Roles Management
        Route::get('roles/data', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'data'])->name('roles.data');
        Route::get('roles/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'trash'])->name('roles.trash');
        Route::get('roles/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'trashData'])->name('roles.trash.data');
        Route::post('roles/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'restore'])->name('roles.restore');
        Route::delete('roles/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'forceDelete'])->name('roles.forceDelete');
        Route::resource('roles', \App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class);

        // Announcements
        Route::get('announcements/data', [AnnouncementController::class, 'data'])->name('announcements.data');
        Route::resource('announcements', AnnouncementController::class)->except(['show']);
        Route::get('clinics/data', [ClinicController::class, 'data'])->name('clinics.data');
        Route::get('clinics/{id}/users/data', [ClinicController::class, 'clinicUsersData'])->name('clinics.users.data');
        Route::put('clinics/{id}/update-status', [ClinicController::class, 'updateStatus'])->name('clinics.update-status');
        Route::put('clinics/{id}/update-is-allowed', [ClinicController::class, 'updateIsAllowed'])->name('clinics.update-is-allowed');
        Route::put('clinics/{id}/toggle-rental-space-company', [ClinicController::class, 'toggleRentalSpaceCompany'])->name('clinics.toggle-rental-space-company');
        Route::get('clinics/{id}/approval', [ClinicController::class, 'showApproval'])->name('clinics.approval');
        Route::resource('clinics', ClinicController::class);

        Route::get('suppliers/data', [SupplierController::class, 'data'])->name('suppliers.data');
        Route::put('suppliers/{id}/update-status', [SupplierController::class, 'updateStatus'])->name('suppliers.update-status');
        Route::put('suppliers/{id}/update-is-allowed', [SupplierController::class, 'updateIsAllowed'])->name('suppliers.update-is-allowed');
        Route::get('suppliers/{id}/approval', [SupplierController::class, 'showApproval'])->name('suppliers.approval');
        Route::resource('suppliers', SupplierController::class);

        // Supplier Products Management
        Route::group(['prefix' => 'supplier-products'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\SupplierProductController::class, 'index'])->name('supplier-products.index');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\SupplierProductController::class, 'data'])->name('supplier-products.data');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\SupplierProductController::class, 'show'])->name('supplier-products.show');
            Route::put('/{id}/approval-status', [\App\Http\Controllers\Backend\Dashboards\Admin\SupplierProductController::class, 'updateApprovalStatus'])->name('supplier-products.update-approval-status');
            Route::get('/supplier/{supplierId}', [\App\Http\Controllers\Backend\Dashboards\Admin\SupplierProductController::class, 'supplierProducts'])->name('supplier-products.by-supplier');
        });

        // Rental Space Management
        Route::get('rental-spaces/data', [RentalSpaceController::class, 'data'])->name('rental-spaces.data');
        Route::get('rental-spaces/trash', [RentalSpaceController::class, 'trash'])->name('rental-spaces.trash');
        Route::get('rental-spaces/trash/data', [RentalSpaceController::class, 'trashData'])->name('rental-spaces.trash.data');
        Route::post('rental-spaces/{id}/restore', [RentalSpaceController::class, 'restore'])->name('rental-spaces.restore');
        Route::put('rental-spaces/{id}/update-status', [RentalSpaceController::class, 'updateStatus'])->name('rental-spaces.update-status');
        Route::delete('rental-spaces/{id}/force-delete', [RentalSpaceController::class, 'forceDelete'])->name('rental-spaces.force-delete');
        Route::resource('rental-spaces', RentalSpaceController::class);

        Route::get('approvements/{id}', [ModuleApprovementController::class, 'getApprovement'])->name('approvements.data');
        Route::post('approvements', [ModuleApprovementController::class, 'storeApprovement'])->name('approvements.store');
        Route::put('approvements/{id}', [ModuleApprovementController::class, 'updateApprovement'])->name('approvements.update');

        // Doctor Profiles Management
        Route::group(['prefix' => 'doctor-profiles'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'data'])->name('doctor-profiles.data');
            Route::get('/pending', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'pending'])->name('doctor-profiles.pending');
            Route::get('/pending/data', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'pendingData'])->name('doctor-profiles.pending.data');
            Route::post('/approve/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'approve'])->name('doctor-profiles.approve');
            Route::post('/reject/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'reject'])->name('doctor-profiles.reject');
            Route::post('/toggle-featured/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'toggleFeatured'])->name('doctor-profiles.toggle-featured');
            Route::post('/toggle-lock/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'toggleLockForEdit'])->name('doctor-profiles.toggle-lock');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'index'])->name('doctor-profiles.index');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'show'])->name('doctor-profiles.show');
        });

        // Notifications Management
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'data'])->name('notifications.data');
            Route::get('/latest', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'getLatest'])->name('notifications.latest');
            Route::post('/mark-as-read/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
            Route::post('/mark-all-as-read', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        });

        // Blogs Management
        Route::get('blog-categories/data', [BlogCategoryController::class, 'data'])->name('blog-categories.data');
        Route::put('blog-categories/{id}/update-status', [BlogCategoryController::class, 'updateStatus'])->name('blog-categories.update-status');
        Route::get('blog-categories/trash', [BlogCategoryController::class, 'trash'])->name('blog-categories.trash');
        Route::get('blog-categories/trash/data', [BlogCategoryController::class, 'trashData'])->name('blog-categories.trash.data');
        Route::post('blog-categories/{id}/restore', [BlogCategoryController::class, 'restore'])->name('blog-categories.restore');
        Route::delete('blog-categories/{id}/force-delete', [BlogCategoryController::class, 'forceDelete'])->name('blog-categories.force-delete');
        Route::resource('blog-categories', BlogCategoryController::class);

        Route::get('blog-posts/data', [BlogPostController::class, 'data'])->name('blog-posts.data');
        Route::put('blog-posts/{id}/update-status', [BlogPostController::class, 'updateStatus'])->name('blog-posts.update-status');
        Route::get('blog-posts/trash', [BlogPostController::class, 'trash'])->name('blog-posts.trash');
        Route::get('blog-posts/trash/data', [BlogPostController::class, 'trashData'])->name('blog-posts.trash.data');
        Route::post('blog-posts/{id}/restore', [BlogPostController::class, 'restore'])->name('blog-posts.restore');
        Route::delete('blog-posts/{id}/force-delete', [BlogPostController::class, 'forceDelete'])->name('blog-posts.force-delete');
        Route::resource('blog-posts', BlogPostController::class);

        // Admin Users Management
        Route::group(['prefix' => 'admin-users'], function () {
            Route::get('/roles', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'roles'])->name('admin-users.roles');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'data'])->name('admin-users.data');
            Route::get('/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'trash'])->name('admin-users.trash');
            Route::get('/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'trashData'])->name('admin-users.trash.data');
            Route::post('/restore/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'restore'])->name('admin-users.restore');
            Route::delete('/force/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'forceDelete'])->name('admin-users.force.delete');
            Route::post('/toggle-status/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'toggleStatus'])->name('admin-users.toggle.status');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'index'])->name('admin-users.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'store'])->name('admin-users.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'show'])->name('admin-users.show');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'update'])->name('admin-users.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'destroy'])->name('admin-users.destroy');
        });

        // Courses Management
        Route::get('courses/data', [CourseController::class, 'data'])->name('courses.data');
        Route::put('courses/{id}/update-status', [CourseController::class, 'updateStatus'])->name('courses.update-status');
        Route::get('courses/trash', [CourseController::class, 'trash'])->name('courses.trash');
        Route::get('courses/trash/data', [CourseController::class, 'trashData'])->name('courses.trash.data');
        Route::post('courses/{id}/restore', [CourseController::class, 'restore'])->name('courses.restore');
        Route::delete('courses/{id}/force-delete', [CourseController::class, 'forceDelete'])->name('courses.force-delete');
        Route::resource('courses', CourseController::class);


        // jobs Management
        Route::get('jobs/data', [JobController::class, 'data'])->name('jobs.data');
        Route::put('jobs/{id}/update-status', [JobController::class, 'updateStatus'])->name('jobs.update-status');
        Route::get('jobs/trash', [JobController::class, 'trash'])->name('jobs.trash');
        Route::get('jobs/trash/data', [JobController::class, 'trashData'])->name('jobs.trash.data');
        Route::post('jobs/{id}/restore', [JobController::class, 'restore'])->name('jobs.restore');
        Route::delete('jobs/{id}/force-delete', [JobController::class, 'forceDelete'])->name('jobs.force-delete');
        Route::resource('jobs', JobController::class);

        // Orders Management
        Route::group(['prefix' => 'orders'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\OrderController::class, 'data'])->name('orders.data');
            Route::get('/{id}/suppliers', [\App\Http\Controllers\Backend\Dashboards\Admin\OrderController::class, 'getOrderSuppliers'])->name('orders.suppliers');
            Route::get('/{id}/items', [\App\Http\Controllers\Backend\Dashboards\Admin\OrderController::class, 'getOrderItems'])->name('orders.items');
            Route::post('/{id}/update-payment-status', [\App\Http\Controllers\Backend\Dashboards\Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
            Route::get('/analytics', [\App\Http\Controllers\Backend\Dashboards\Admin\OrderController::class, 'analytics'])->name('orders.analytics');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\OrderController::class, 'index'])->name('orders.index');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\OrderController::class, 'show'])->name('orders.show');
        });

        // Purchase Requests & Offers
        Route::group(['prefix' => 'purchase-requests'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\PurchaseRequestController::class, 'data'])->name('purchase-requests.data');
            Route::get('/{id}/offers', [\App\Http\Controllers\Backend\Dashboards\Admin\PurchaseRequestController::class, 'offers'])->name('purchase-requests.offers');
            Route::get('/{id}/offers/data', [\App\Http\Controllers\Backend\Dashboards\Admin\PurchaseRequestController::class, 'offersData'])->name('purchase-requests.offers.data');
        });

        // Tickets Management
        Route::get('tickets/data', [\App\Http\Controllers\Backend\Dashboards\Admin\TicketController::class, 'data'])->name('tickets.data');
        Route::put('tickets/{id}/update-status', [\App\Http\Controllers\Backend\Dashboards\Admin\TicketController::class, 'updateStatus'])->name('tickets.update-status');
        Route::post('tickets/{id}/reply', [\App\Http\Controllers\Backend\Dashboards\Admin\TicketController::class, 'reply'])->name('tickets.reply');
        Route::get('tickets/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\TicketController::class, 'trash'])->name('tickets.trash');
        Route::get('tickets/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\TicketController::class, 'trashData'])->name('tickets.trash.data');
        Route::post('tickets/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Admin\TicketController::class, 'restore'])->name('tickets.restore');
        Route::delete('tickets/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Admin\TicketController::class, 'forceDelete'])->name('tickets.force-delete');
        Route::resource('tickets', \App\Http\Controllers\Backend\Dashboards\Admin\TicketController::class)->only(['index', 'show', 'destroy']);

        // Governorates Management
        Route::get('governorates/data', [\App\Http\Controllers\Backend\Dashboards\Admin\GovernorateController::class, 'data'])->name('governorates.data');
        Route::get('governorates/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\GovernorateController::class, 'trash'])->name('governorates.trash');
        Route::get('governorates/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\GovernorateController::class, 'trashData'])->name('governorates.trash.data');
        Route::post('governorates/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Admin\GovernorateController::class, 'restore'])->name('governorates.restore');
        Route::delete('governorates/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Admin\GovernorateController::class, 'forceDelete'])->name('governorates.force-delete');
        Route::resource('governorates', \App\Http\Controllers\Backend\Dashboards\Admin\GovernorateController::class);

        // Cities Management
        Route::get('cities/data', [\App\Http\Controllers\Backend\Dashboards\Admin\CityController::class, 'data'])->name('cities.data');
        Route::get('cities/get-cities', [\App\Http\Controllers\Backend\Dashboards\Admin\CityController::class, 'getCitiesByGovernorateId'])->name('cities.get-cities-by-governorate-id');
        Route::get('cities/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\CityController::class, 'trash'])->name('cities.trash');
        Route::get('cities/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\CityController::class, 'trashData'])->name('cities.trash.data');
        Route::post('cities/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Admin\CityController::class, 'restore'])->name('cities.restore');
        Route::delete('cities/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Admin\CityController::class, 'forceDelete'])->name('cities.force-delete');
        Route::resource('cities', \App\Http\Controllers\Backend\Dashboards\Admin\CityController::class);

        // Areas Management
        Route::get('areas/data', [\App\Http\Controllers\Backend\Dashboards\Admin\AreaController::class, 'data'])->name('areas.data');
        Route::get('areas/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\AreaController::class, 'trash'])->name('areas.trash');
        Route::get('areas/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\AreaController::class, 'trashData'])->name('areas.trash.data');
        Route::post('areas/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Admin\AreaController::class, 'restore'])->name('areas.restore');
        Route::delete('areas/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Admin\AreaController::class, 'forceDelete'])->name('areas.force-delete');
        Route::resource('areas', \App\Http\Controllers\Backend\Dashboards\Admin\AreaController::class);

        Route::get('course-enrollments', [\App\Http\Controllers\Backend\Dashboards\Admin\CourseEnrollmentController::class, 'index'])->name('course-enrollments.index');
        Route::get('course-enrollments/data', [\App\Http\Controllers\Backend\Dashboards\Admin\CourseEnrollmentController::class, 'data'])->name('course-enrollments.data');
        Route::put('course-enrollments/{id}/status', [\App\Http\Controllers\Backend\Dashboards\Admin\CourseEnrollmentController::class, 'updateStatus'])->name('course-enrollments.update-status');
        Route::delete('course-enrollments/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\CourseEnrollmentController::class, 'destroy'])->name('course-enrollments.destroy');

        // Translations Management
        Route::group(['prefix' => 'translations', 'as' => 'translations.'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\TranslationController::class, 'index'])->name('index');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\TranslationController::class, 'data'])->name('data');
            Route::post('/update', [\App\Http\Controllers\Backend\Dashboards\Admin\TranslationController::class, 'update'])->name('update');
            Route::post('/store', [\App\Http\Controllers\Backend\Dashboards\Admin\TranslationController::class, 'store'])->name('store');
            Route::post('/scan', [\App\Http\Controllers\Backend\Dashboards\Admin\TranslationController::class, 'scan'])->name('scan');
            Route::delete('/destroy', [\App\Http\Controllers\Backend\Dashboards\Admin\TranslationController::class, 'destroy'])->name('destroy');
        });

        // Subscription Management
        Route::group(['prefix' => 'subscriptions'], function () {
            Route::get('analytics', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'analytics'])->name('subscriptions.analytics');
            // Plans Management
            Route::get('plans/data', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'data'])->name('plans.data');
            Route::get('plans/create', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'create'])->name('plans.create');
            Route::post('plans', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'store'])->name('plans.store');
            Route::get('plans/{id}/edit', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'edit'])->name('plans.edit');
            Route::put('plans/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'update'])->name('plans.update');
            Route::delete('plans/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'destroy'])->name('plans.destroy');
            Route::get('plans/{id}/features', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'manageFeatures'])->name('plans.features');
            Route::post('plans/{planId}/features', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'addFeature'])->name('plans.features.add');
            Route::put('plans/{planId}/features/{featureId}', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'updateFeature'])->name('plans.features.update');
            Route::delete('plans/{planId}/features/{featureId}', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'deleteFeature'])->name('plans.features.delete');
            Route::get('plans', [\App\Http\Controllers\Backend\Dashboards\Admin\PlanController::class, 'index'])->name('plans.index');

            // Features Management
            Route::get('features/data', [\App\Http\Controllers\Backend\Dashboards\Admin\FeatureMasterController::class, 'data'])->name('features.data');
            Route::get('features/create', [\App\Http\Controllers\Backend\Dashboards\Admin\FeatureMasterController::class, 'create'])->name('features.create');
            Route::post('features', [\App\Http\Controllers\Backend\Dashboards\Admin\FeatureMasterController::class, 'store'])->name('features.store');
            Route::get('features/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\FeatureMasterController::class, 'show'])->name('features.show');
            Route::put('features/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\FeatureMasterController::class, 'update'])->name('features.update');
            Route::delete('features/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\FeatureMasterController::class, 'destroy'])->name('features.destroy');
            Route::get('features', [\App\Http\Controllers\Backend\Dashboards\Admin\FeatureMasterController::class, 'index'])->name('features.index');

            // Subscriptions Management
            Route::get('subscriptions/data', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'data'])->name('subscriptions.data');
            Route::get('subscriptions/create', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'create'])->name('subscriptions.create');
            Route::get('subscriptions/entities', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'getEntities'])->name('subscriptions.entities');
            Route::post('subscriptions', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'store'])->name('subscriptions.store');
            Route::post('subscriptions/{id}/extend', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'extend'])->name('subscriptions.extend');
            Route::post('subscriptions/{id}/cancel', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'cancel'])->name('subscriptions.cancel');
            Route::delete('subscriptions/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'destroy'])->name('subscriptions.destroy');
            Route::get('subscriptions', [\App\Http\Controllers\Backend\Dashboards\Admin\SubscriptionManagementController::class, 'index'])->name('subscriptions.index');
        });

        // Affiliate Management
        Route::group(['prefix' => 'affiliates'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateCodeController::class, 'index'])->name('affiliates.index');
            Route::get('/users', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateUserController::class, 'index'])
                ->name('affiliates.users.index');
            Route::get('/users/{affiliateCode}', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateUserController::class, 'show'])
                ->whereNumber('affiliateCode')
                ->name('affiliates.users.show');
            Route::get('/settings', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateSettingsController::class, 'index'])->name('affiliates.settings');
            Route::post('/settings', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateSettingsController::class, 'update'])->name('affiliates.settings.update');
            Route::get('/payouts', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliatePayoutController::class, 'index'])
                ->name('affiliates.payouts');
            Route::post('/payouts/{payoutRequest}/mark-paid', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliatePayoutController::class, 'markPaid'])
                ->whereNumber('payoutRequest')
                ->name('affiliates.payouts.mark-paid');
            Route::post('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateCodeController::class, 'update'])
                ->whereNumber('id')
                ->name('affiliates.update');
        });

        // Banners Management
        Route::group(['prefix' => 'banners'], function () {
            Route::get('/', [BannerController::class, 'index'])->name('banners.index');
            Route::get('/data', [BannerController::class, 'data'])->name('banners.data');
            Route::get('/create', [BannerController::class, 'create'])->name('banners.create');
            Route::post('/store', [BannerController::class, 'store'])->name('banners.store');
            Route::get('/{id}/edit', [BannerController::class, 'edit'])->name('banners.edit');
            Route::put('/{id}/update', [BannerController::class, 'update'])->name('banners.update');
            Route::delete('/{id}/destroy', [BannerController::class, 'destroy'])->name('banners.destroy');
            Route::get('/trash', [BannerController::class, 'trash'])->name('banners.trash');
            Route::get('/trash/data', [BannerController::class, 'trashData'])->name('banners.trash.data');
            Route::post('/{id}/restore', [BannerController::class, 'restore'])->name('banners.restore');
            Route::delete('/{id}/force-delete', [BannerController::class, 'forceDelete'])->name('banners.force-delete');
            Route::post('/{id}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
        });
    }
);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/admin',
        'as' => 'admin.',
        'namespace' => 'App\Http\Controllers\Backend\Dashboards\Admin',
        'middleware' => [
            'auth:admin',
            'verified',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ],
    function () {
        Route::get('/affiliates/users', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateUserController::class, 'index'])
            ->name('affiliates.users.index');
        Route::get('/affiliates/users/{affiliateCode}', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateUserController::class, 'show'])
            ->whereNumber('affiliateCode')
            ->name('affiliates.users.show');
        Route::get('/affiliates/settings', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateSettingsController::class, 'index'])
            ->name('affiliates.settings');
        Route::post('/affiliates/settings', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliateSettingsController::class, 'update'])
            ->name('affiliates.settings.update');
        Route::get('/affiliates/payouts', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliatePayoutController::class, 'index'])
            ->name('affiliates.payouts');
        Route::post('/affiliates/payouts/{payoutRequest}/mark-paid', [\App\Http\Controllers\Backend\Dashboards\Admin\AffiliatePayoutController::class, 'markPaid'])
            ->whereNumber('payoutRequest')
            ->name('affiliates.payouts.mark-paid');
    }
);


Route::post('/admin/logout', function (Request $request) {
    Auth::guard('admin')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/admin/login');
})->name('admin.logout');
