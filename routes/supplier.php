<?php

use App\Http\Controllers\Backend\Dashboards\Supplier\DashboardController;
use App\Http\Controllers\Backend\Dashboards\Supplier\SupplierController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/supplier',
        'as' => 'supplier.',
        'namespace' => 'App\Http\Controllers\Backend\Dashboards\Supplier',
        'middleware' => [   
            'auth:supplier',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/register-supplier', function () {
            return view('backend.dashboards.supplier.auth.register-supplier');
        })->name('register-supplier')->withoutMiddleware('auth:supplier');

        Route::post('/register-supplier', [SupplierController::class, 'registerSupplier'])
            ->name('register-supplier')->withoutMiddleware('auth:supplier');
    }
);


Route::post('/supplier/logout', function (Request $request) {
    Auth::guard('supplier')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/');
})->name('supplier.logout');
