<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Frontend\HomeController;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ],
    function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        Route::get('/products', function () {
            return view('frontend.pages.products');
        })->name('products');

        Route::get('/clinics', function () {
            return view('frontend.pages.clinics');
        })->name('clinics');

        Route::get('/suppliers', function () {
            return view('frontend.pages.suppliers');
        })->name('suppliers');

        Route::get('/jobs', function () {
            return view('frontend.pages.jobs');
        })->name('jobs');

        Route::get('/blog', function () {
            return view('frontend.pages.blog');
        })->name('blog');

        Route::get('/courses', function () {
            return view('frontend.pages.courses');
        })->name('courses');
    });


require __DIR__ . '/admin.php';
require __DIR__.'/clinic.php';
require __DIR__.'/supplier.php';
