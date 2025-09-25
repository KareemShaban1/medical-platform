<?php

use App\Http\Controllers\Backend\Dashboards\Supplier\DashboardController;
use App\Http\Controllers\Backend\Dashboards\Supplier\SupplierController;
use App\Http\Controllers\Backend\Dashboards\Supplier\ProductController;
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

        Route::post('/verify-otp', [SupplierController::class, 'verifyOtp'])
            ->name('verify-otp')->withoutMiddleware('auth:supplier')
            ->middleware('throttle:3,5');

        Route::post('/resend-otp', [SupplierController::class, 'resendOtp'])
            ->name('resend-otp')->withoutMiddleware('auth:supplier')
            ->middleware('throttle:1,1');

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

    }
);





    Route::post('/supplier/logout', function (Request $request) {
        Auth::guard('supplier')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    })->name('supplier.logout');

