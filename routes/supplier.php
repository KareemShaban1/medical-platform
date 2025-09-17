<?php

use App\Http\Controllers\Backend\Dashboards\Supplier\DashboardController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/supplier',
        'as' => 'supplier.',
        'namespace' => 'App\Http\Controllers\Backend\Dashboards\Supplier',
        'middleware' => [   
            'auth:supplier',
            'verified',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
    }
);