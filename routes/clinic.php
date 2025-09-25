<?php

use App\Http\Controllers\Backend\Dashboards\Clinic\DashboardController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ClinicController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Dashboards\Clinic\RoleController;
use App\Http\Controllers\Backend\Dashboards\Clinic\RentalSpaceController;
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
            'localeViewPath'
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
            })->name('register-clinic')->withoutMiddleware('auth:clinic');

            Route::post('/register-clinic', [ClinicController::class, 'registerClinic'])
                ->name('register-clinic')->withoutMiddleware('auth:clinic');

            Route::post('/verify-otp', [ClinicController::class, 'verifyOtp'])
                ->name('verify-otp')->withoutMiddleware('auth:clinic')
                ->middleware('throttle:2,5');

            Route::post('/resend-otp', [ClinicController::class, 'resendOtp'])
                ->name('resend-otp')->withoutMiddleware('auth:clinic')
                ->middleware('throttle:1,1');

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
    }
);


Route::post('/clinic/logout', function (Request $request) {
    Auth::guard('clinic')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/clinic/login');
})->name('clinic.logout');


