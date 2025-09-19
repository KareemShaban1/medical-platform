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
    }
);

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

        Route::post('/supplier/logout', function (Request $request) {
            Auth::guard('supplier')->logout();
        
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        
            return redirect()->to('/');
        })->name('supplier.logout');
        
