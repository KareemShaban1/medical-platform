<?php

use App\Http\Controllers\Backend\Dashboards\Clinic\DashboardController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ClinicController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Dashboards\Clinic\RoleController;
use App\Http\Controllers\Backend\Dashboards\Clinic\RentalSpaceController;
use App\Http\Controllers\Backend\Dashboards\Clinic\JobController;
use App\Http\Controllers\Backend\Dashboards\Clinic\JobApplicationFieldController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ClinicInventoryController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ClinicInventoryMovementsController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ClinicUserSalaryController;
use App\Http\Controllers\Backend\Dashboards\Clinic\SalaryContractController;
use App\Http\Controllers\Backend\Dashboards\Clinic\PayslipController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ExpenseCategoryController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ExpenseController;
use App\Http\Controllers\Backend\Dashboards\Clinic\PrescriptionController;
use App\Http\Controllers\Backend\Dashboards\Clinic\AnnouncementController as ClinicAnnouncementController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ClinicInfoController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Backend\Dashboards\Clinic\CourseEnrollmentController as ClinicCourseEnrollmentController;
use App\Http\Controllers\Backend\Dashboards\Clinic\OrderController as ClinicOrderController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ForgotPasswordController;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/clinic',
        'as' => 'clinic.',
        'namespace' => 'App\Http\Controllers\Backend\Dashboards\Clinic',
        'middleware' => [
            'auth:clinic',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath',
            'check.clinic.approval'
        ]
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Clinic Info Settings
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/clinic-info', [ClinicInfoController::class, 'index'])->name('settings.clinic-info');
            Route::put('/clinic-info', [ClinicInfoController::class, 'update'])->name('settings.clinic-info.update');
        });

        // Roles Management
        Route::get('roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::resource('roles', RoleController::class);

        // Announcements - dismiss
        Route::post('announcements/{id}/dismiss', [ClinicAnnouncementController::class, 'dismiss'])->name('announcements.dismiss');

        // Rental Space Management
        Route::group(['middleware' => 'check.subscription:rental_spaces_module'], function () {
            Route::get('rental-spaces/data', [RentalSpaceController::class, 'data'])->name('rental-spaces.data');
            Route::put('rental-spaces/{id}/update-status', [RentalSpaceController::class, 'updateStatus'])->name('rental-spaces.update-status');
            Route::resource('rental-spaces', RentalSpaceController::class);
        });

        // Clinic Orders (website orders made by clinic users)
        Route::get('orders', [ClinicOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [ClinicOrderController::class, 'show'])->name('orders.show');

        // Subscription Management
        Route::group(['prefix' => 'subscriptions'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\SubscriptionController::class, 'index'])->name('subscriptions.index');
            Route::get('/plans', [\App\Http\Controllers\Backend\Dashboards\Clinic\SubscriptionController::class, 'plans'])->name('subscriptions.plans');
            Route::post('/subscribe/{planId}', [\App\Http\Controllers\Backend\Dashboards\Clinic\SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
            Route::post('/cancel', [\App\Http\Controllers\Backend\Dashboards\Clinic\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
            Route::get('/usage', [\App\Http\Controllers\Backend\Dashboards\Clinic\SubscriptionController::class, 'usage'])->name('subscriptions.usage');
        });

        // Affiliate
        Route::get('/affiliate', [\App\Http\Controllers\Backend\Dashboards\Clinic\AffiliateController::class, 'index'])
            ->name('affiliate.index');
        Route::post('/affiliate/payout-requests', [\App\Http\Controllers\Backend\Dashboards\Clinic\AffiliatePayoutController::class, 'store'])
            ->name('affiliate.payouts.store');

        // Users Management
        Route::group(['prefix' => 'users'], function () {
            Route::get('/roles', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'roles'])->name('users.roles');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'data'])->name('users.data');
            Route::post('/toggle-status/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'toggleStatus'])->name('users.toggle.status');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'index'])->name('users.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'store'])->name('users.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'show'])->name('users.show');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'update'])->name('users.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'destroy'])->name('users.destroy');
        });

        // Working Hours Management
        Route::group(['prefix' => 'working-hours'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\WorkingHourController::class, 'index'])->name('working-hours.index');
            Route::get('/for-user/{clinicUserId}', [\App\Http\Controllers\Backend\Dashboards\Clinic\WorkingHourController::class, 'forUser'])->name('working-hours.for-user');
            Route::post('/bulk-save', [\App\Http\Controllers\Backend\Dashboards\Clinic\WorkingHourController::class, 'bulkSave'])->name('working-hours.bulk-save');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\WorkingHourController::class, 'destroy'])->name('working-hours.destroy');
        });

        // My Course Enrollments
        Route::get('course-enrollments', [ClinicCourseEnrollmentController::class, 'index'])->name('course-enrollments.index');

        // Attendance Management
        Route::group(['prefix' => 'attendance'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/check-in', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
            Route::post('/check-out', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'checkOut'])->name('attendance.check-out');
            Route::post('/absence', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'absence'])->name('attendance.absence');
            Route::get('/{id}/attachments', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'attachments'])->name('attendance.attachments');
            Route::post('/{id}/approve', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'approve'])->name('attendance.approve');
            Route::post('/{id}/approve-check-in', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'approveCheckIn'])->name('attendance.approve-check-in');
            Route::post('/{id}/approve-check-out', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'approveCheckOut'])->name('attendance.approve-check-out');
            Route::get('/my-logs', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'myLogs'])->name('attendance.my-logs');
            Route::get('/compute', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'compute'])->name('attendance.compute');
        });

        Route::get('/register-clinic', function () {
            return view('backend.dashboards.clinic.auth.register-clinic');
        })->name('register-clinic')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);

        Route::post('/register-clinic', [ClinicController::class, 'registerClinic'])
            ->name('register-clinic')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);

        Route::post('/verify-otp', [ClinicController::class, 'verifyOtp'])
            ->name('verify-otp')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);
        //->middleware('throttle:2,5');
    
        Route::post('/resend-otp', [ClinicController::class, 'resendOtp'])
            ->name('resend-otp')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);
        // ->middleware('throttle:1,1');
    
        // Forgot Password Routes
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPassword'])
            ->name('forgot-password')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp'])
            ->name('forgot-password.send')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);
        Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyOtp'])
            ->name('forgot-password.verify')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);
        Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])
            ->name('forgot-password.reset')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);
        Route::post('/forgot-password/resend', [ForgotPasswordController::class, 'resendOtp'])
            ->name('forgot-password.resend')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);

        // Location dropdowns endpoints
        Route::get('/governorates', [ClinicController::class, 'getGovernorates'])
            ->name('governorates')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);
        Route::get('/cities', [ClinicController::class, 'getCities'])
            ->name('cities')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);
        Route::get('/areas', [ClinicController::class, 'getAreas'])
            ->name('areas')->withoutMiddleware(['auth:clinic', 'check.clinic.approval']);

        // Approval routes (without approval middleware)
        Route::get('/approval', [\App\Http\Controllers\Backend\Dashboards\Clinic\ApprovalController::class, 'show'])
            ->name('approval.show')->withoutMiddleware('check.clinic.approval');

        Route::post('/approval/upload', [\App\Http\Controllers\Backend\Dashboards\Clinic\ApprovalController::class, 'upload'])
            ->name('approval.upload')->withoutMiddleware('check.clinic.approval');

        // Doctor Profiles Management
        Route::group(['prefix' => 'doctor-profiles'], function () {
            Route::get('/my-profile', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'myProfile'])->name('doctor-profiles.my-profile');

            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'data'])->name('doctor-profiles.data');
            Route::post('/submit/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'submit'])->name('doctor-profiles.submit');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'index'])->name('doctor-profiles.index');
            Route::get('/create', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'create'])->name('doctor-profiles.create');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'store'])->name('doctor-profiles.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'show'])->name('doctor-profiles.show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'edit'])->name('doctor-profiles.edit');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'update'])->name('doctor-profiles.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\DoctorProfileController::class, 'destroy'])->name('doctor-profiles.destroy');
        });

        // Notifications Management
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\NotificationController::class, 'data'])->name('notifications.data');
            Route::get('/latest', [\App\Http\Controllers\Backend\Dashboards\Clinic\NotificationController::class, 'getLatest'])->name('notifications.latest');
            Route::post('/mark-as-read/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
            Route::post('/mark-all-as-read', [\App\Http\Controllers\Backend\Dashboards\Clinic\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        });

        // Jobs Management (feature: post_jobs)
        Route::middleware('check.subscription:post_jobs')->group(function () {
            Route::get('jobs/data', [JobController::class, 'data'])->name('jobs.data');
            Route::put('jobs/{id}/update-status', [JobController::class, 'updateStatus'])->name('jobs.update-status');
            Route::get('jobs/{id}/applicants', [JobController::class, 'applicants'])->name('jobs.applicants');
            Route::get('job-applications/{applicationId}/details', [JobController::class, 'getApplicationDetails'])->name('job-applications.details');
            Route::post('job-applications/update-status', [JobController::class, 'updateApplicationStatus'])->name('job-applications.update-status');
            Route::put('job-applications/{applicationId}/update-data', [JobController::class, 'updateApplicationData'])->name('job-applications.update-data');
            Route::resource('jobs', JobController::class);
        });

        // Requests Management (feature: purchase_requests)
        Route::middleware('check.subscription:purchase_requests')->group(function () {
            Route::group(['prefix' => 'requests'], function () {
                Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'data'])->name('requests.data');
                Route::get('/categories', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'getCategories'])->name('requests.categories');
                Route::post('/{id}/accept-offer', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'acceptOffer'])->name('requests.accept-offer');
                Route::post('/{id}/process-offer-payment', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'processOfferPayment'])->name('requests.process-offer-payment');
                Route::get('/{id}/payment-return/{gateway}', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'offerPaymentReturn'])->name('requests.payment-return');
                Route::post('/{id}/payment-callback/{gateway}', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'offerPaymentCallback'])->name('requests.payment-callback');
                Route::post('/{id}/cancel', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'cancel'])->name('requests.cancel');
                Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'index'])->name('requests.index');
                Route::get('/create', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'create'])->name('requests.create');
                Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'store'])->name('requests.store');
                Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'show'])->name('requests.show');
                Route::get('/{id}/edit', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'edit'])->name('requests.edit');
                Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'update'])->name('requests.update');
                Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'destroy'])->name('requests.destroy');
            });
        });

        // Accepted Offer Invoice
        Route::get('offers/{offerId}/invoice', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'invoice'])
            ->name('offers.invoice');

        // Patients Management
        Route::group(['prefix' => 'patients'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'data'])->name('patients.data');
            Route::get('/{id}/edit', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'edit'])->name('patients.edit');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'index'])->name('patients.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'store'])->name('patients.store')->middleware('check.subscription:max_patients');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'show'])->name('patients.show');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'update'])->name('patients.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'destroy'])->name('patients.destroy');
        });
        // Job Application Fields Management
        Route::put('job-application-fields/{id}/update-status', [JobApplicationFieldController::class, 'updateStatus'])->name('job-application-fields.update-status');
        Route::get('job-application-fields/{id}/create', [JobApplicationFieldController::class, 'create'])->name('job-application-fields.create');
        Route::get('job-application-fields/{id}/edit', [JobApplicationFieldController::class, 'edit'])->name('job-application-fields.edit');
        Route::post('job-application-fields/store', [JobApplicationFieldController::class, 'store'])->name('job-application-fields.store');
        Route::put('job-application-fields/{id}/update', [JobApplicationFieldController::class, 'update'])->name('job-application-fields.update');
        Route::get('job-application-fields/{id}/show', [JobApplicationFieldController::class, 'show'])->name('job-application-fields.show');

        // Clinic Inventory Management
        Route::get('clinic-inventories/data', [ClinicInventoryController::class, 'data'])->name('clinic-inventories.data');
        Route::resource('clinic-inventories', ClinicInventoryController::class)->except(['store']);
        Route::post('clinic-inventories', [ClinicInventoryController::class, 'store'])
            ->middleware('check.subscription:inventory_module')
            ->name('clinic-inventories.store');

        // Clinic Inventory Movement Management
        Route::group(['middleware' => 'check.subscription:inventory_module'], function () {
            Route::get('clinic-inventory-movements/data/{id}', [ClinicInventoryMovementsController::class, 'data'])->name('clinic-inventory-movements.data');
            Route::get('clinic-inventory-movements/index/{id}', [ClinicInventoryMovementsController::class, 'index'])->name('clinic-inventory-movements.index');
            Route::get('clinic-inventory-movements/create/{id}', [ClinicInventoryMovementsController::class, 'create'])->name('clinic-inventory-movements.create');
            Route::post('clinic-inventory-movements/store', [ClinicInventoryMovementsController::class, 'store'])->name('clinic-inventory-movements.store');
            Route::get('clinic-inventory-movements/edit/{id}', [ClinicInventoryMovementsController::class, 'edit'])->name('clinic-inventory-movements.edit');
            Route::put('clinic-inventory-movements/update/{id}', [ClinicInventoryMovementsController::class, 'update'])->name('clinic-inventory-movements.update');
            Route::get('clinic-inventory-movements/show/{id}', [ClinicInventoryMovementsController::class, 'show'])->name('clinic-inventory-movements.show');
            Route::delete('clinic-inventory-movements/{id}', [ClinicInventoryMovementsController::class, 'destroy'])->name('clinic-inventory-movements.destroy');
        });


        // Clinic User Salaries Management
        Route::get('clinic-user-salaries/data', [ClinicUserSalaryController::class, 'data'])->name('clinic-user-salaries.data');
        Route::get('clinic-user-salaries/user/{userId}/salary-data', [ClinicUserSalaryController::class, 'getUserSalaryData'])->name('clinic-user-salaries.user-salary-data');
        Route::resource('clinic-user-salaries', ClinicUserSalaryController::class);

        // Salary Contracts Management
        Route::get('salary-contracts/data', [SalaryContractController::class, 'data'])->name('salary-contracts.data');
        Route::resource('salary-contracts', SalaryContractController::class);


        //Payslip
        Route::get('payslips/data', [PayslipController::class, 'data'])->name('payslips.data');
        Route::get('payslips/create/{userId}', [PayslipController::class, 'create'])->name('payslips.create');
        Route::get('payslips/edit/{id}', [PayslipController::class, 'edit'])->name('payslips.edit');
        Route::resource('payslips', PayslipController::class);

        // Availability Overrides Management
        Route::get('availability-overrides/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\AvailabilityOverrideController::class, 'data'])->name('availability-overrides.data');
        Route::get('availability-overrides/doctor/{doctorId}', [\App\Http\Controllers\Backend\Dashboards\Clinic\AvailabilityOverrideController::class, 'forDoctor'])->name('availability-overrides.for-doctor');
        Route::resource('availability-overrides', \App\Http\Controllers\Backend\Dashboards\Clinic\AvailabilityOverrideController::class);

        // Daily Periods Management
        Route::get('daily-periods/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\DailyPeriodController::class, 'data'])->name('daily-periods.data');
        Route::get('daily-periods/{id}/appointments', [\App\Http\Controllers\Backend\Dashboards\Clinic\DailyPeriodController::class, 'viewAppointments'])->name('daily-periods.appointments');
        Route::post('daily-periods/{id}/toggle-open', [\App\Http\Controllers\Backend\Dashboards\Clinic\DailyPeriodController::class, 'toggleOpen'])->name('daily-periods.toggle-open');
        Route::post('daily-periods/{id}/update-capacity', [\App\Http\Controllers\Backend\Dashboards\Clinic\DailyPeriodController::class, 'updateCapacity'])->name('daily-periods.update-capacity');
        Route::post('daily-periods/generate', [\App\Http\Controllers\Backend\Dashboards\Clinic\DailyPeriodController::class, 'generatePeriods'])->name('daily-periods.generate');
        Route::resource('daily-periods', \App\Http\Controllers\Backend\Dashboards\Clinic\DailyPeriodController::class);

        // Appointments Management
        Route::group(['middleware' => 'check.subscription:appointments_module'], function () {
            Route::get('appointments/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\AppointmentController::class, 'data'])->name('appointments.data');
            Route::get('appointments/{doctorId}/analytics', [\App\Http\Controllers\Backend\Dashboards\Clinic\AppointmentController::class, 'analytics'])->name('appointments.analytics');
            Route::get('appointments/available-periods', [\App\Http\Controllers\Backend\Dashboards\Clinic\AppointmentController::class, 'getAvailablePeriods'])->name('appointments.available-periods');
            Route::post('appointments/{id}/confirm', [\App\Http\Controllers\Backend\Dashboards\Clinic\AppointmentController::class, 'confirm'])->name('appointments.confirm');
            Route::post('appointments/{id}/cancel', [\App\Http\Controllers\Backend\Dashboards\Clinic\AppointmentController::class, 'cancel'])->name('appointments.cancel');
            Route::get('appointments/doctor/{doctorId}', [\App\Http\Controllers\Backend\Dashboards\Clinic\AppointmentController::class, 'forDoctor'])->name('appointments.for-doctor');
            Route::get('appointments/period/{periodId}', [\App\Http\Controllers\Backend\Dashboards\Clinic\AppointmentController::class, 'forPeriod'])->name('appointments.for-period');
            Route::resource('appointments', \App\Http\Controllers\Backend\Dashboards\Clinic\AppointmentController::class);
        });

        Route::group(['middleware' => 'check.subscription:expenses_module'], function () {
            // Expense Categories Management
            Route::get('expense-categories/data', [ExpenseCategoryController::class, 'data'])->name('expense-categories.data');
            Route::put('expense-categories/{id}/update-status', [ExpenseCategoryController::class, 'updateStatus'])->name('expense-categories.update-status');
            Route::resource('expense-categories', ExpenseCategoryController::class);

            // Expenses Management
            Route::get('expenses/data', [ExpenseController::class, 'data'])->name('expenses.data');
            Route::get('expenses/analytics', [ExpenseController::class, 'analytics'])->name('expenses.analytics');
            Route::resource('expenses', ExpenseController::class);
        });
        // Lab Orders
        Route::group(['middleware' => 'check.subscription:lab_module'], function () {
            Route::get('lab-orders', [\App\Http\Controllers\Backend\Dashboards\Clinic\LabOrderController::class, 'index'])->name('lab-orders.index');
            Route::get('lab-orders/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\LabOrderController::class, 'data'])->name('lab-orders.data');
            Route::get('lab-orders/create', [\App\Http\Controllers\Backend\Dashboards\Clinic\LabOrderController::class, 'create'])->name('lab-orders.create');
            Route::post('lab-orders', [\App\Http\Controllers\Backend\Dashboards\Clinic\LabOrderController::class, 'store'])->name('lab-orders.store');
            Route::get('lab-orders/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\LabOrderController::class, 'show'])->name('lab-orders.show');
            Route::post('lab-orders/{id}/upload', [\App\Http\Controllers\Backend\Dashboards\Clinic\LabOrderController::class, 'upload'])->name('lab-orders.upload');
            Route::post('lab-orders/{id}/complete', [\App\Http\Controllers\Backend\Dashboards\Clinic\LabOrderController::class, 'complete'])->name('lab-orders.complete');
        });

        // Medical Records
    
        // Invoices
        Route::get('invoices', [\App\Http\Controllers\Backend\Dashboards\Clinic\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\InvoiceController::class, 'data'])->name('invoices.data');
        Route::get('invoices/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{id}/header', [\App\Http\Controllers\Backend\Dashboards\Clinic\InvoiceController::class, 'updateHeader'])->name('invoices.update-header');
        Route::post('invoices/{id}/items', [\App\Http\Controllers\Backend\Dashboards\Clinic\InvoiceController::class, 'addItem'])->name('invoices.items.add');
        Route::post('invoices/{id}/items/{itemId}', [\App\Http\Controllers\Backend\Dashboards\Clinic\InvoiceController::class, 'updateItem'])->name('invoices.items.update');
        Route::delete('invoices/{id}/items/{itemId}', [\App\Http\Controllers\Backend\Dashboards\Clinic\InvoiceController::class, 'deleteItem'])->name('invoices.items.delete');
        Route::post('invoices/{id}/mark-paid', [\App\Http\Controllers\Backend\Dashboards\Clinic\InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::group(['prefix' => 'medical-records', 'middleware' => 'check.subscription:medical_records_module'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\MedicalRecordController::class, 'data'])->name('medical-records.data');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\MedicalRecordController::class, 'index'])->name('medical-records.index');
            Route::get('/{appointment}', [\App\Http\Controllers\Backend\Dashboards\Clinic\MedicalRecordController::class, 'edit'])->name('medical-records.edit');
            Route::put('/{appointment}', [\App\Http\Controllers\Backend\Dashboards\Clinic\MedicalRecordController::class, 'update'])->name('medical-records.update');
            Route::post('/share/{record}', [\App\Http\Controllers\Backend\Dashboards\Clinic\MedicalRecordController::class, 'toggleShare'])->name('medical-records.share');
        });
        // Prescriptions Management
        Route::group(['middleware' => 'check.subscription:prescriptions_module'], function () {
            Route::get('prescriptions/data', [PrescriptionController::class, 'data'])->name('prescriptions.data');
            Route::get('prescriptions/create/{appointmentId}', [PrescriptionController::class, 'create'])->name('prescriptions.create');
            Route::post('prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
            Route::get('prescriptions/{id}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
            Route::get('prescriptions/{id}/edit', [PrescriptionController::class, 'edit'])->name('prescriptions.edit');
            Route::put('prescriptions/{id}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
            Route::delete('prescriptions/{id}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
            Route::get('prescriptions/print/{appointmentId}', [PrescriptionController::class, 'print'])->name('prescriptions.print');
            Route::get('prescriptions/download/{appointmentId}', [PrescriptionController::class, 'downloadPdf'])->name('prescriptions.download');
        });

        // Tickets Management
        Route::group(['prefix' => 'tickets'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\TicketController::class, 'data'])->name('tickets.data');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\TicketController::class, 'index'])->name('tickets.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\TicketController::class, 'store'])->name('tickets.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\TicketController::class, 'show'])->name('tickets.show');
            Route::post('/{id}/reply', [\App\Http\Controllers\Backend\Dashboards\Clinic\TicketController::class, 'reply'])->name('tickets.reply');
        });
    }
);


Route::post('/clinic/logout', function (Request $request) {
    Auth::guard('clinic')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/clinic/login');
})->name('clinic.logout');
