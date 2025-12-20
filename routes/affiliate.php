<?php

use App\Http\Controllers\Backend\Dashboards\Affiliate\DashboardController;
use App\Http\Controllers\Backend\Dashboards\Affiliate\AuthController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/affiliate',
        'as' => 'affiliate.',
        'namespace' => 'App\Http\Controllers\Backend\Dashboards\Affiliate',
        'middleware' => [
            'auth:affiliate',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath',
        ]
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/payout-requests', [\App\Http\Controllers\Backend\Dashboards\Affiliate\PayoutRequestController::class, 'store'])
            ->name('payouts.store');
    }
);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/affiliate',
        'as' => 'affiliate.',
        'namespace' => 'App\Http\Controllers\Backend\Dashboards\Affiliate',
        'middleware' => [
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath',
        ]
    ],
    function () {
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    }
);

Route::post('/affiliate/logout', function (Request $request) {
    Auth::guard('affiliate')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('affiliate.logout');
