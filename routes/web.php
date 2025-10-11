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
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ClinicUser\ProfileController;

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
		Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
		Route::post('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
		Route::get('/products/category/{categoryId}', [ProductController::class, 'category'])->name('products.category');
		Route::get('/products/supplier/{supplierId}', [ProductController::class, 'supplier'])->name('products.supplier');
		Route::get('/products/on-sale', [ProductController::class, 'onSale'])->name('products.on-sale');
		Route::get('/products/in-stock', [ProductController::class, 'inStock'])->name('products.in-stock');
		Route::get('/products/recent', [ProductController::class, 'recent'])->name('products.recent');

		Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics');
		Route::get('/clinics/{id}', [ClinicController::class, 'show'])->name('clinics.show');
		Route::post('/clinics/filter', [ClinicController::class, 'filter'])->name('clinics.filter');

		Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
		Route::get('/suppliers/{id}', [SupplierController::class, 'show'])->name('suppliers.show');
		Route::post('/suppliers/filter', [SupplierController::class, 'filter'])->name('suppliers.filter');

		Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
		Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
		Route::post('/jobs/filter', [JobController::class, 'filter'])->name('jobs.filter');
		Route::get('/jobs/{id}/apply', [JobController::class, 'application'])->name('jobs.application');
		Route::post('/jobs/{id}/apply', [JobController::class, 'submitApplication'])->name('jobs.submit-application');
		Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
		Route::get('/blogs/{id}', [BlogController::class, 'show'])->name('blogs.show');
		Route::post('/blogs/filter', [BlogController::class, 'filter'])->name('blogs.filter');

		Route::get('/courses', [CourseController::class, 'index'])->name('courses');
		Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
		Route::post('/courses/filter', [CourseController::class, 'filter'])->name('courses.filter');
    });

// Cart and Checkout Routes (requires clinic authentication)
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'auth:clinic',
        'localeCookieRedirect',
        'localizationRedirect',
        'localeViewPath'
    ]
], function () {
    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/data', [CartController::class, 'getData'])->name('cart.data');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Profile routes
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/orders/{id}', [ProfileController::class, 'orderDetails'])->name('profile.order-details');
});

// Patient Dashboard Routes
Route::group([
    'prefix' => LaravelLocalization::setLocale() . '/user',
    'as' => 'user.',
    'middleware' => [
        'auth:patient',
        'localeCookieRedirect',
        'localizationRedirect',
        'localeViewPath'
    ]
], function () {
    Route::get('/', [\App\Http\Controllers\Frontend\Patient\DashboardController::class, 'index'])
        ->name('dashboard');

    // Tickets Management
    Route::get('tickets/data', [\App\Http\Controllers\Frontend\TicketController::class, 'data'])->name('tickets.data');
    Route::post('tickets/{id}/reply', [\App\Http\Controllers\Frontend\TicketController::class, 'reply'])->name('tickets.reply');
    Route::resource('tickets', \App\Http\Controllers\Frontend\TicketController::class)->only(['index', 'store', 'show']);
});

// Patient Logout Route
Route::post('/user/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('patient')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->to('/login');
})->name('user.logout');

require __DIR__ . '/admin.php';
require __DIR__.'/clinic.php';
require __DIR__.'/supplier.php';