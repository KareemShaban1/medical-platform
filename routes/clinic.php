<?php

use App\Http\Controllers\Backend\Dashboards\Clinic\DashboardController;
use App\Http\Controllers\Backend\Dashboards\Clinic\ClinicController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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
            Route::get('roles/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\RoleController::class, 'data'])->name('roles.data');
            Route::get('roles/trash', [\App\Http\Controllers\Backend\Dashboards\Clinic\RoleController::class, 'trash'])->name('roles.trash');
            Route::get('roles/trash/data', [\App\Http\Controllers\Backend\Dashboards\Clinic\RoleController::class, 'trashData'])->name('roles.trash.data');
            Route::post('roles/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Clinic\RoleController::class, 'restore'])->name('roles.restore');
            Route::delete('roles/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Clinic\RoleController::class, 'forceDelete'])->name('roles.forceDelete');
            Route::resource('roles', \App\Http\Controllers\Backend\Dashboards\Clinic\RoleController::class);


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
    }
);


Route::post('/clinic/logout', function (Request $request) {
    Auth::guard('clinic')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/clinic/login');
})->name('clinic.logout');


