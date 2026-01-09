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

        // Tickets Management
        Route::group(['prefix' => 'tickets'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Affiliate\TicketController::class, 'data'])->name('tickets.data');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Affiliate\TicketController::class, 'index'])->name('tickets.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Affiliate\TicketController::class, 'store'])->name('tickets.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Affiliate\TicketController::class, 'show'])->name('tickets.show');
            Route::post('/{id}/reply', [\App\Http\Controllers\Backend\Dashboards\Affiliate\TicketController::class, 'reply'])->name('tickets.reply');
        });
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

        // Forgot Password Routes
        Route::get('/forgot-password', [\App\Http\Controllers\Backend\Dashboards\Affiliate\ForgotPasswordController::class, 'showForgotPassword'])
            ->name('forgot-password');
        Route::post('/forgot-password', [\App\Http\Controllers\Backend\Dashboards\Affiliate\ForgotPasswordController::class, 'sendResetOtp'])
            ->name('forgot-password.send');
        Route::post('/forgot-password/verify', [\App\Http\Controllers\Backend\Dashboards\Affiliate\ForgotPasswordController::class, 'verifyOtp'])
            ->name('forgot-password.verify');
        Route::post('/forgot-password/reset', [\App\Http\Controllers\Backend\Dashboards\Affiliate\ForgotPasswordController::class, 'resetPassword'])
            ->name('forgot-password.reset');
        Route::post('/forgot-password/resend', [\App\Http\Controllers\Backend\Dashboards\Affiliate\ForgotPasswordController::class, 'resendOtp'])
            ->name('forgot-password.resend');
    }
);

Route::post('/affiliate/logout', function (Request $request) {
    Auth::guard('affiliate')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/affiliate/login');
})->name('affiliate.logout');
