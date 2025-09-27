<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\JobController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ClinicController;
use App\Http\Controllers\Frontend\SupplierController;
use App\Http\Controllers\Frontend\CourseController;

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

		Route::get('/products', [ProductController::class, 'index'])->name('products');
		Route::post('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
		Route::get('/products/category/{categoryId}', [ProductController::class, 'category'])->name('products.category');
		Route::get('/products/supplier/{supplierId}', [ProductController::class, 'supplier'])->name('products.supplier');
		Route::get('/products/on-sale', [ProductController::class, 'onSale'])->name('products.on-sale');
		Route::get('/products/in-stock', [ProductController::class, 'inStock'])->name('products.in-stock');
		Route::get('/products/recent', [ProductController::class, 'recent'])->name('products.recent');

		Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics');
		Route::post('/clinics/filter', [ClinicController::class, 'filter'])->name('clinics.filter');

		Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
		Route::post('/suppliers/filter', [SupplierController::class, 'filter'])->name('suppliers.filter');

		Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
		Route::post('/jobs/filter', [JobController::class, 'filter'])->name('jobs.filter');

		Route::get('/blog', [BlogController::class, 'index'])->name('blog');
		Route::post('/blog/filter', [BlogController::class, 'filter'])->name('blog.filter');

		Route::get('/courses', [CourseController::class, 'index'])->name('courses');
		Route::post('/courses/filter', [CourseController::class, 'filter'])->name('courses.filter');
    });


require __DIR__ . '/admin.php';
require __DIR__.'/clinic.php';
require __DIR__.'/supplier.php';
