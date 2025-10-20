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
use Illuminate\Support\Facades\Auth;

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

        // Roles Management
        Route::get('roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::get('roles/trash', [RoleController::class, 'trash'])->name('roles.trash');
        Route::get('roles/trash/data', [RoleController::class, 'trashData'])->name('roles.trash.data');
        Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
        Route::delete('roles/{id}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.forceDelete');
        Route::resource('roles', RoleController::class);

        // Rental Space Management
        Route::get('rental-spaces/data', [RentalSpaceController::class, 'data'])->name('rental-spaces.data');
        Route::get('rental-spaces/trash', [RentalSpaceController::class, 'trash'])->name('rental-spaces.trash');
        Route::get('rental-spaces/trash/data', [RentalSpaceController::class, 'trashData'])->name('rental-spaces.trash.data');
        Route::post('rental-spaces/{id}/restore', [RentalSpaceController::class, 'restore'])->name('rental-spaces.restore');
        Route::delete('rental-spaces/{id}/force-delete', [RentalSpaceController::class, 'forceDelete'])->name('rental-spaces.force-delete');
        Route::put('rental-spaces/{id}/update-status', [RentalSpaceController::class, 'updateStatus'])->name('rental-spaces.update-status');
        Route::resource('rental-spaces', RentalSpaceController::class);

        // Users Management
        Route::group(['prefix' => 'users'], function () {
            Route::get('/roles', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'roles'])->name('users.roles');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'data'])->name('users.data');
            Route::get('/trash', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'trash'])->name('users.trash');
            Route::get('/trash/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'trashData'])->name('users.trash.data');
            Route::post('/restore/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'restore'])->name('users.restore');
            Route::delete('/force/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\UserController::class, 'forceDelete'])->name('users.force.delete');
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

        // Attendance Management
        Route::group(['prefix' => 'attendance'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/check-in', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
            Route::post('/check-out', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'checkOut'])->name('attendance.check-out');
            Route::post('/absence', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'absence'])->name('attendance.absence');
            Route::get('/{id}/attachments', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'attachments'])->name('attendance.attachments');
            Route::post('/{id}/approve', [\App\Http\Controllers\Backend\Dashboards\Clinic\AttendanceController::class, 'approve'])->name('attendance.approve');
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

        // Approval routes (without approval middleware)
        Route::get('/approval', [\App\Http\Controllers\Backend\Dashboards\Clinic\ApprovalController::class, 'show'])
            ->name('approval.show')->withoutMiddleware('check.clinic.approval');

        Route::post('/approval/upload', [\App\Http\Controllers\Backend\Dashboards\Clinic\ApprovalController::class, 'upload'])
            ->name('approval.upload')->withoutMiddleware('check.clinic.approval');

        // Doctor Profiles Management
        Route::group(['prefix' => 'doctor-profiles'], function () {
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

        // Jobs Management
        Route::get('jobs/data', [JobController::class, 'data'])->name('jobs.data');
        Route::get('jobs/trash', [JobController::class, 'trash'])->name('jobs.trash');
        Route::get('jobs/trash/data', [JobController::class, 'trashData'])->name('jobs.trash.data');
        Route::post('jobs/{id}/restore', [JobController::class, 'restore'])->name('jobs.restore');
        Route::delete('jobs/{id}/force-delete', [JobController::class, 'forceDelete'])->name('jobs.force-delete');
        Route::put('jobs/{id}/update-status', [JobController::class, 'updateStatus'])->name('jobs.update-status');
        Route::get('jobs/{id}/applicants', [JobController::class, 'applicants'])->name('jobs.applicants');
        Route::post('job-applications/update-status', [JobController::class, 'updateApplicationStatus'])->name('job-applications.update-status');
        Route::resource('jobs', JobController::class);

        // Requests Management (Tickets System)
        Route::group(['prefix' => 'requests'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'data'])->name('requests.data');
            Route::get('/categories', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'getCategories'])->name('requests.categories');
            Route::post('/{id}/accept-offer', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'acceptOffer'])->name('requests.accept-offer');
            Route::post('/{id}/cancel', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'cancel'])->name('requests.cancel');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'index'])->name('requests.index');
            Route::get('/create', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'create'])->name('requests.create');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'store'])->name('requests.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'show'])->name('requests.show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'edit'])->name('requests.edit');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'update'])->name('requests.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Clinic\RequestController::class, 'destroy'])->name('requests.destroy');
        });

        // Patients Management
        Route::group(['prefix' => 'patients'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'data'])->name('patients.data');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'index'])->name('patients.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Clinic\PatientController::class, 'store'])->name('patients.store');
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
        Route::get('clinic-inventories/trash', [ClinicInventoryController::class, 'trash'])->name('clinic-inventories.trash');
        Route::get('clinic-inventories/trash/data', [ClinicInventoryController::class, 'trashData'])->name('clinic-inventories.trash.data');
        Route::post('clinic-inventories/{id}/restore', [ClinicInventoryController::class, 'restore'])->name('clinic-inventories.restore');
        Route::delete('clinic-inventories/{id}/force-delete', [ClinicInventoryController::class, 'forceDelete'])->name('clinic-inventories.force-delete');
        Route::resource('clinic-inventories', ClinicInventoryController::class);

        // Clinic Inventory Movement Management
        Route::get('clinic-inventory-movements/data/{id}', [ClinicInventoryMovementsController::class, 'data'])->name('clinic-inventory-movements.data');
        Route::get('clinic-inventory-movements/index/{id}', [ClinicInventoryMovementsController::class, 'index'])->name('clinic-inventory-movements.index');
        Route::get('clinic-inventory-movements/trash', [ClinicInventoryMovementsController::class, 'trash'])->name('clinic-inventory-movements.trash');
        Route::get('clinic-inventory-movements/trash/data', [ClinicInventoryMovementsController::class, 'trashData'])->name('clinic-inventory-movements.trash.data');
        Route::post('clinic-inventory-movements/{id}/restore', [ClinicInventoryMovementsController::class, 'restore'])->name('clinic-inventory-movements.restore');
        Route::delete('clinic-inventory-movements/{id}/force-delete', [ClinicInventoryMovementsController::class, 'forceDelete'])->name('clinic-inventory-movements.force-delete');
        Route::get('clinic-inventory-movements/create/{id}', [ClinicInventoryMovementsController::class, 'create'])->name('clinic-inventory-movements.create');
        Route::post('clinic-inventory-movements/store', [ClinicInventoryMovementsController::class, 'store'])->name('clinic-inventory-movements.store');
        Route::get('clinic-inventory-movements/edit/{id}', [ClinicInventoryMovementsController::class, 'edit'])->name('clinic-inventory-movements.edit');
        Route::put('clinic-inventory-movements/update/{id}', [ClinicInventoryMovementsController::class, 'update'])->name('clinic-inventory-movements.update');
        Route::get('clinic-inventory-movements/show/{id}', [ClinicInventoryMovementsController::class, 'show'])->name('clinic-inventory-movements.show');
        Route::delete('clinic-inventory-movements/{id}', [ClinicInventoryMovementsController::class, 'destroy'])->name('clinic-inventory-movements.destroy');


        // Clinic User Salaries Management
        Route::get('clinic-user-salaries/data', [ClinicUserSalaryController::class, 'data'])->name('clinic-user-salaries.data');
        Route::get('clinic-user-salaries/trash', [ClinicUserSalaryController::class, 'trash'])->name('clinic-user-salaries.trash');
        Route::get('clinic-user-salaries/trash/data', [ClinicUserSalaryController::class, 'trashData'])->name('clinic-user-salaries.trash.data');
        Route::post('clinic-user-salaries/{id}/restore', [ClinicUserSalaryController::class, 'restore'])->name('clinic-user-salaries.restore');
        Route::delete('clinic-user-salaries/{id}/force-delete', [ClinicUserSalaryController::class, 'forceDelete'])->name('clinic-user-salaries.force-delete');
        Route::get('clinic-user-salaries/user/{userId}/salary-data', [ClinicUserSalaryController::class, 'getUserSalaryData'])->name('clinic-user-salaries.user-salary-data');
        Route::resource('clinic-user-salaries', ClinicUserSalaryController::class);

        // Salary Contracts Management
        Route::get('salary-contracts/data', [SalaryContractController::class, 'data'])->name('salary-contracts.data');
        Route::get('salary-contracts/trash', [SalaryContractController::class, 'trash'])->name('salary-contracts.trash');
        Route::get('salary-contracts/trash/data', [SalaryContractController::class, 'trashData'])->name('salary-contracts.trash.data');
        Route::post('salary-contracts/{id}/restore', [SalaryContractController::class, 'restore'])->name('salary-contracts.restore');
        Route::delete('salary-contracts/{id}/force-delete', [SalaryContractController::class, 'forceDelete'])->name('salary-contracts.force-delete');
        Route::resource('salary-contracts', SalaryContractController::class);


        //Payslip
        Route::get('payslips/data', [PayslipController::class, 'data'])->name('payslips.data');
        Route::get('payslips/create/{userId}', [PayslipController::class, 'create'])->name('payslips.create');
        Route::get('payslips/edit/{id}', [PayslipController::class, 'edit'])->name('payslips.edit');
        Route::get('payslips/trash', [PayslipController::class, 'trash'])->name('payslips.trash');
        Route::get('payslips/trash/data', [PayslipController::class, 'trashData'])->name('payslips.trash.data');
        Route::post('payslips/{id}/restore', [PayslipController::class, 'restore'])->name('payslips.restore');
        Route::delete('payslips/{id}/force-delete', [PayslipController::class, 'forceDelete'])->name('payslips.force-delete');
        Route::resource('payslips', PayslipController::class);

        // Expense Categories Management
        Route::get('expense-categories/data', [ExpenseCategoryController::class, 'data'])->name('expense-categories.data');
        Route::get('expense-categories/trash', [ExpenseCategoryController::class, 'trash'])->name('expense-categories.trash');
        Route::get('expense-categories/trash/data', [ExpenseCategoryController::class, 'trashData'])->name('expense-categories.trash.data');
        Route::post('expense-categories/{id}/restore', [ExpenseCategoryController::class, 'restore'])->name('expense-categories.restore');
        Route::delete('expense-categories/{id}/force-delete', [ExpenseCategoryController::class, 'forceDelete'])->name('expense-categories.force-delete');
        Route::put('expense-categories/{id}/update-status', [ExpenseCategoryController::class, 'updateStatus'])->name('expense-categories.update-status');
        Route::resource('expense-categories', ExpenseCategoryController::class);

        // Expenses Management
        Route::get('expenses/data', [ExpenseController::class, 'data'])->name('expenses.data');
        Route::get('expenses/trash', [ExpenseController::class, 'trash'])->name('expenses.trash');
        Route::get('expenses/trash/data', [ExpenseController::class, 'trashData'])->name('expenses.trash.data');
        Route::post('expenses/{id}/restore', [ExpenseController::class, 'restore'])->name('expenses.restore');
        Route::delete('expenses/{id}/force-delete', [ExpenseController::class, 'forceDelete'])->name('expenses.force-delete');
        Route::resource('expenses', ExpenseController::class);
    }

);


Route::post('/clinic/logout', function (Request $request) {
    Auth::guard('clinic')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/clinic/login');
})->name('clinic.logout');
