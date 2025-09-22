<?php

use App\Http\Controllers\Backend\Dashboards\Admin\DashboardController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Dashboards\Admin\CategoryController;
use App\Http\Controllers\Backend\Dashboards\Admin\ClinicController;
use App\Http\Controllers\Backend\Dashboards\Admin\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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

        Route::get('categories/data', [CategoryController::class, 'data'])->name('categories.data');
        Route::post('categories/update-status', [CategoryController::class, 'updateStatus'])->name('categories.update-status');
        Route::resource('categories', CategoryController::class);

        // Roles Management
        Route::get('roles/data', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'data'])->name('roles.data');
        Route::get('roles/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'trash'])->name('roles.trash');
        Route::get('roles/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'trashData'])->name('roles.trash.data');
        Route::post('roles/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'restore'])->name('roles.restore');
        Route::delete('roles/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'forceDelete'])->name('roles.forceDelete');
        Route::resource('roles', \App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class);

        Route::get('clinics/data', [ClinicController::class, 'data'])->name('clinics.data');
        Route::get('clinics/{id}/users/data', [ClinicController::class, 'clinicUsersData'])->name('clinics.users.data');
        Route::post('clinics/update-status', [ClinicController::class, 'updateStatus'])->name('clinics.update-status');
        Route::patch('clinics/{id}/toggle-status', [ClinicController::class, 'toggleStatus'])->name('clinics.toggle.status');
        Route::patch('clinics/{id}/toggle-is-allowed', [ClinicController::class, 'toggleIsAllowed'])->name('clinics.toggle.is-allowed');
        Route::resource('clinics', ClinicController::class);

        Route::get('suppliers/data', [SupplierController::class, 'data'])->name('suppliers.data');
        Route::post('suppliers/update-status', [SupplierController::class, 'updateStatus'])->name('suppliers.update-status');
        Route::resource('suppliers', SupplierController::class);

        // Doctor Profiles Management
        Route::group(['prefix' => 'doctor-profiles'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'data'])->name('doctor-profiles.data');
            Route::get('/pending', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'pending'])->name('doctor-profiles.pending');
            Route::get('/pending/data', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'pendingData'])->name('doctor-profiles.pending.data');
            Route::post('/approve/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'approve'])->name('doctor-profiles.approve');
            Route::post('/reject/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'reject'])->name('doctor-profiles.reject');
            Route::post('/toggle-featured/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'toggleFeatured'])->name('doctor-profiles.toggle-featured');
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

    }
);


Route::post('/admin/logout', function (Request $request) {
    Auth::guard('admin')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/');
})->name('admin.logout');
