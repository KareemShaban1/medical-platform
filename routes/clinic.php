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

            Route::get('/register-clinic', function () {
                return view('backend.dashboards.clinic.auth.register-clinic');
            })->name('register-clinic')->withoutMiddleware(['auth:clinic' , 'check.clinic.approval']);

            Route::post('/register-clinic', [ClinicController::class, 'registerClinic'])
                ->name('register-clinic')->withoutMiddleware(['auth:clinic' , 'check.clinic.approval']);

            Route::post('/verify-otp', [ClinicController::class, 'verifyOtp'])
                ->name('verify-otp')->withoutMiddleware(['auth:clinic' , 'check.clinic.approval']);
                //->middleware('throttle:2,5');

            Route::post('/resend-otp', [ClinicController::class, 'resendOtp'])
                ->name('resend-otp')->withoutMiddleware(['auth:clinic' , 'check.clinic.approval']);
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

    }
);


Route::post('/clinic/logout', function (Request $request) {
    Auth::guard('clinic')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/clinic/login');
})->name('clinic.logout');
