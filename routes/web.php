<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\JobController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\ClinicController;
use App\Http\Controllers\Frontend\CourseController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\RentalSpaceController as FrontendRentalSpaceController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\SupplierController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Frontend\Auth\PatientAuthController;
use App\Http\Controllers\Frontend\ClinicUser\ProfileController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Backend\PaymentController;
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
		Route::get('/terms-of-use', [HomeController::class, 'termsOfUse'])->name('terms');
		Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy');
		Route::get('/return-policy', [HomeController::class, 'returnPolicy'])->name('return-policy');
		Route::get('/shipping-policy', [HomeController::class, 'shippingPolicy'])->name('shipping-policy');
		Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('about');

		// Contact Form
		Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

		Route::get('/products', [ProductController::class, 'index'])->name('products');
		Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
		Route::post('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
		Route::get('/products/category/{categoryId}', [ProductController::class, 'category'])->name('products.category');
		Route::get('/products/supplier/{supplierId}', [ProductController::class, 'supplier'])->name('products.supplier');
		Route::get('/products/on-sale', [ProductController::class, 'onSale'])->name('products.on-sale');
		Route::get('/products/in-stock', [ProductController::class, 'inStock'])->name('products.in-stock');
		Route::get('/products/recent', [ProductController::class, 'recent'])->name('products.recent');

		// Rental Spaces (Frontend)
		Route::get('/rental-spaces', [FrontendRentalSpaceController::class, 'index'])->name('rental-spaces');
		Route::get('/rental-spaces/{id}', [FrontendRentalSpaceController::class, 'show'])->name('rental-spaces.show');
		Route::post('/rental-spaces/filter', [FrontendRentalSpaceController::class, 'filter'])->name('rental-spaces.filter');

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

		// Subscription Plans (Public)
		// Route::get('/subscriptions/plans', [\App\Http\Controllers\Frontend\SubscriptionController::class, 'plans'])->name('subscriptions.plans');

		// Doctor Profile Routes
		Route::get('/doctors', [\App\Http\Controllers\Frontend\DoctorController::class, 'index'])->name('doctors.index');
		Route::post('/doctors/filter', [\App\Http\Controllers\Frontend\DoctorController::class, 'filter'])->name('doctors.filter');
		Route::get('/doctors/{id}', [\App\Http\Controllers\Frontend\DoctorProfileController::class, 'show'])->name('doctors.show');
		Route::get('/doctors/{id}/available-days', [\App\Http\Controllers\Frontend\DoctorProfileController::class, 'getAvailableDays'])->name('doctors.available-days');
		Route::get('/doctors/{id}/available-periods', [\App\Http\Controllers\Frontend\DoctorProfileController::class, 'getAvailablePeriods'])->name('doctors.available-periods');

		// Appointment Routes (public for booking, authenticated for managing)
		Route::post('/appointments/book', [\App\Http\Controllers\Frontend\AppointmentController::class, 'book'])->name('appointments.book');
		Route::post('/appointments/confirm', [\App\Http\Controllers\Frontend\AppointmentController::class, 'confirm'])->name('appointments.confirm');
    });

// Course enrollment (Clinic users only)
Route::group([
    'prefix' => Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale(),
    'middleware' => [
        'auth:clinic',
        'localeCookieRedirect',
        'localizationRedirect',
        'localeViewPath'
    ]
], function () {
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll'])
        ->middleware('check.subscription:enroll_courses')
        ->name('courses.enroll');
});

// Subscription enrollment (Clinic, Doctor, or Supplier users) - AJAX route without localization middleware
Route::post('/subscriptions/plans/{planId}/subscribe', [\App\Http\Controllers\Frontend\SubscriptionController::class, 'subscribe'])
    ->middleware('web')
    ->name('subscriptions.subscribe');

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
    Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

    // Profile routes (for clinic users with clinics)
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/orders/{id}', [ProfileController::class, 'orderDetails'])->name('profile.order-details');
});

// Doctor Routes (standalone doctors without clinics)
Route::group([
    'prefix' => LaravelLocalization::setLocale() . '/doctor',
    'as' => 'doctor.',
    'middleware' => [
        'auth:clinic',
        'localeCookieRedirect',
        'localizationRedirect',
        'localeViewPath'
    ]
], function () {
    Route::get('/dashboard', [\App\Http\Controllers\Frontend\Doctor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Frontend\Doctor\ProfileController::class, 'index'])->name('profile.index');

    // Doctor Subscriptions
    Route::get('/subscriptions', [\App\Http\Controllers\Frontend\Doctor\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/cancel', [\App\Http\Controllers\Frontend\Doctor\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::get('/profile/create', [\App\Http\Controllers\Frontend\Doctor\ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile', [\App\Http\Controllers\Frontend\Doctor\ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [\App\Http\Controllers\Frontend\Doctor\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Frontend\Doctor\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/submit', [\App\Http\Controllers\Frontend\Doctor\ProfileController::class, 'submit'])->name('profile.submit');
    Route::get('/orders', [\App\Http\Controllers\Frontend\Doctor\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}/details', [\App\Http\Controllers\Frontend\Doctor\OrderController::class, 'orderDetails'])->name('orders.show');
    Route::get('/courses', [\App\Http\Controllers\Frontend\Doctor\CourseController::class, 'index'])->name('courses.index');
});

// Doctor Registration Routes (public)
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeCookieRedirect',
        'localizationRedirect',
        'localeViewPath'
    ]
], function () {
    Route::get('/doctor/register', [\App\Http\Controllers\Frontend\Doctor\DoctorAuthController::class, 'showRegisterForm'])->name('doctor.register.show');
    Route::post('/doctor/register', [\App\Http\Controllers\Frontend\Doctor\DoctorAuthController::class, 'register'])->name('doctor.register');
});

// Payment gateway callbacks (no auth required for webhooks and returns)
Route::post('/payment/callback/{gateway}', [PaymentController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/payment/return/{gateway}', [PaymentController::class, 'paymentReturn'])->name('payment.return');

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

    // My Appointments
    Route::get('appointments', [\App\Http\Controllers\Frontend\AppointmentController::class, 'myAppointments'])->name('appointments.my');
    Route::post('appointments/{id}/cancel', [\App\Http\Controllers\Frontend\AppointmentController::class, 'cancel'])->name('appointments.cancel');

    Route::get('lab-orders', [\App\Http\Controllers\Frontend\Patient\LabOrderController::class, 'index'])->name('lab-orders.index');
    Route::get('lab-orders/{id}', [\App\Http\Controllers\Frontend\Patient\LabOrderController::class, 'show'])->name('lab-orders.show');
    Route::get('medical-records', [\App\Http\Controllers\Frontend\Patient\MedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::get('medical-records/{record}', [\App\Http\Controllers\Frontend\Patient\MedicalRecordController::class, 'show'])->name('medical-records.show');
    // Prescriptions (Patient)
    Route::get('prescriptions', [\App\Http\Controllers\Frontend\Patient\PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('prescriptions/{id}', [\App\Http\Controllers\Frontend\Patient\PrescriptionController::class, 'show'])->name('prescriptions.show');

    // Profile Information (Patient)
    Route::get('profile', [\App\Http\Controllers\Frontend\Patient\ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [\App\Http\Controllers\Frontend\Patient\ProfileController::class, 'update'])->name('profile.update');
    // Change Password (Patient)
    Route::get('profile/password', [\App\Http\Controllers\Frontend\Patient\ProfileController::class, 'password'])->name('profile.password');
    Route::put('profile/password', [\App\Http\Controllers\Frontend\Patient\ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

    // Location dropdowns endpoints
    Route::get('/governorates', [HomeController::class, 'getGovernorates'])
    ->name('getGovernorates');
Route::get('/cities', [HomeController::class, 'getCities'])
    ->name('getCities');
Route::get('/areas', [HomeController::class, 'getAreas'])
    ->name('getAreas');


// Patient Logout Route
Route::post('/user/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('patient')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->to('/');
})->name('user.logout');



Route::post('/register', [PatientAuthController::class, 'register'])->name('register');


require __DIR__ . '/admin.php';
require __DIR__.'/clinic.php';
require __DIR__.'/supplier.php';
require __DIR__.'/affiliate.php';
