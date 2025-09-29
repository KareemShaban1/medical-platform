<?php

use App\Http\Controllers\Backend\Dashboards\Supplier\DashboardController;
use App\Http\Controllers\Backend\Dashboards\Supplier\SupplierController;
use App\Http\Controllers\Backend\Dashboards\Supplier\ProductController;
use App\Http\Controllers\Backend\Dashboards\Supplier\ApprovalController;
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
            'localeViewPath',
            'check.supplier.approval'
        ]
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Add other supplier dashboard routes here...

        Route::get('/register-supplier', function () {
            return view('backend.dashboards.supplier.auth.register-supplier');
        })->name('register-supplier')->withoutMiddleware(['auth:supplier' , 'check.supplier.approval']);

        Route::post('/register-supplier', [SupplierController::class, 'registerSupplier'])
            ->name('register-supplier')->withoutMiddleware(['auth:supplier' , 'check.supplier.approval']);

        Route::post('/verify-otp', [SupplierController::class, 'verifyOtp'])
            ->name('verify-otp')->withoutMiddleware(['auth:supplier' , 'check.supplier.approval'])
            ->middleware('throttle:3,5');

        Route::post('/resend-otp', [SupplierController::class, 'resendOtp'])
            ->name('resend-otp')->withoutMiddleware(['auth:supplier' , 'check.supplier.approval'])
            ->middleware('throttle:1,1');


        // Approval routes (without approval middleware)
        Route::get('/approval', [ApprovalController::class, 'show'])
        ->name('approval.show')->withoutMiddleware('check.supplier.approval');

        Route::post('/approval/upload', [ApprovalController::class, 'upload'])
        ->name('approval.upload')->withoutMiddleware('check.supplier.approval');


        Route::group(['prefix' => 'products'], function () {
            Route::get('/categories', [ProductController::class, 'categories'])->name('products.categories');
            Route::get('/data', [ProductController::class, 'data'])->name('products.data');
            Route::get('/trash/list', [ProductController::class, 'trash'])->name('products.trash');
            Route::get('/trash/data', [ProductController::class, 'trashData'])->name('products.trash.data');
            Route::post('/restore/{id}', [ProductController::class, 'restore'])->name('products.restore');
            Route::delete('/force/{id}', [ProductController::class, 'forceDelete'])->name('products.force.delete');
            Route::get('/', [ProductController::class, 'index'])->name('products.index');
            Route::post('/', [ProductController::class, 'store'])->name('products.store');
            Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
            Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
            Route::patch('/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle.status');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        });

        // Users Management
        Route::group(['prefix' => 'users'], function () {
            Route::get('/roles', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'roles'])->name('users.roles');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'data'])->name('users.data');
            Route::get('/trash', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'trash'])->name('users.trash');
            Route::get('/trash/data', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'trashData'])->name('users.trash.data');
            Route::post('/restore/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'restore'])->name('users.restore');
            Route::delete('/force/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'forceDelete'])->name('users.force.delete');
            Route::post('/toggle-status/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'toggleStatus'])->name('users.toggle.status');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'index'])->name('users.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'store'])->name('users.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'show'])->name('users.show');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'update'])->name('users.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\UserController::class, 'destroy'])->name('users.destroy');
        });

        // Roles Management
        Route::get('roles/data', [\App\Http\Controllers\Backend\Dashboards\Supplier\RoleController::class, 'data'])->name('roles.data');
        Route::get('roles/trash', [\App\Http\Controllers\Backend\Dashboards\Supplier\RoleController::class, 'trash'])->name('roles.trash');
        Route::get('roles/trash/data', [\App\Http\Controllers\Backend\Dashboards\Supplier\RoleController::class, 'trashData'])->name('roles.trash.data');
        Route::post('roles/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Supplier\RoleController::class, 'restore'])->name('roles.restore');
        Route::delete('roles/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Supplier\RoleController::class, 'forceDelete'])->name('roles.forceDelete');
        Route::resource('roles', \App\Http\Controllers\Backend\Dashboards\Supplier\RoleController::class);

        // Specialized Categories Management
        Route::group(['prefix' => 'specialized-categories'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Supplier\SpecializedCategoryController::class, 'data'])->name('specialized-categories.data');
            Route::get('/available', [\App\Http\Controllers\Backend\Dashboards\Supplier\SpecializedCategoryController::class, 'getAvailableCategories'])->name('specialized-categories.available');
            Route::post('/attach', [\App\Http\Controllers\Backend\Dashboards\Supplier\SpecializedCategoryController::class, 'attachToCategory'])->name('specialized-categories.attach');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Supplier\SpecializedCategoryController::class, 'index'])->name('specialized-categories.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Supplier\SpecializedCategoryController::class, 'store'])->name('specialized-categories.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\SpecializedCategoryController::class, 'show'])->name('specialized-categories.show');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\SpecializedCategoryController::class, 'update'])->name('specialized-categories.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\SpecializedCategoryController::class, 'destroy'])->name('specialized-categories.destroy');
        });

        // Available Requests (for suppliers to view and submit offers)
        Route::group(['prefix' => 'available-requests'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'availableRequestsData'])->name('available-requests.data');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'availableRequests'])->name('available-requests.index');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'showAvailableRequest'])->name('available-requests.show');
            Route::get('/{id}/create-offer', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'createOfferForRequest'])->name('available-requests.create-offer');
        });

        // Offers Management
        Route::group(['prefix' => 'offers'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'data'])->name('offers.data');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'index'])->name('offers.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'store'])->name('offers.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'show'])->name('offers.show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'edit'])->name('offers.edit');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'update'])->name('offers.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\OfferController::class, 'destroy'])->name('offers.destroy');
        });

        // Notifications Management
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Supplier\NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Supplier\NotificationController::class, 'data'])->name('notifications.data');
            Route::get('/latest', [\App\Http\Controllers\Backend\Dashboards\Supplier\NotificationController::class, 'getLatest'])->name('notifications.latest');
            Route::post('/mark-as-read/{id}', [\App\Http\Controllers\Backend\Dashboards\Supplier\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
            Route::post('/mark-all-as-read', [\App\Http\Controllers\Backend\Dashboards\Supplier\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        });


    }
);





    Route::post('/supplier/logout', function (Request $request) {
        Auth::guard('supplier')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    })->name('supplier.logout');

