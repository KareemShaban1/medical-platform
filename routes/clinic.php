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


