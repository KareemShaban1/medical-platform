<?php

use App\Http\Controllers\Backend\Dashboards\Admin\BlogCategoryController;
use App\Http\Controllers\Backend\Dashboards\Admin\DashboardController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Dashboards\Admin\CategoryController;
use App\Http\Controllers\Backend\Dashboards\Admin\ClinicController;
use App\Http\Controllers\Backend\Dashboards\Admin\SupplierController;
use App\Http\Controllers\Backend\Dashboards\Admin\RentalSpaceController;
use App\Http\Controllers\Backend\Dashboards\Admin\ModuleApprovementController;
use App\Http\Controllers\Backend\Dashboards\Admin\BlogPostController;
use App\Http\Controllers\Backend\Dashboards\Admin\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/admin',
        'as' => 'admin.',
        'namespace' => 'App\Http\Controllers\Backend\Dashboards\Admin',
        'middleware' => [
            'auth:admin',
            'verified',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('categories/data', [CategoryController::class, 'data'])->name('categories.data');
        Route::put('categories/{id}/update-status', [CategoryController::class, 'updateStatus'])->name('categories.update-status');
        Route::resource('categories', CategoryController::class);

        // Roles Management
        Route::get('roles/data', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'data'])->name('roles.data');
        Route::get('roles/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'trash'])->name('roles.trash');
        Route::get('roles/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'trashData'])->name('roles.trash.data');
        Route::post('roles/{id}/restore', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'restore'])->name('roles.restore');
        Route::delete('roles/{id}/force-delete', [\App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class, 'forceDelete'])->name('roles.forceDelete');
        Route::resource('roles', \App\Http\Controllers\Backend\Dashboards\Admin\RoleController::class);

        Route::get('clinics/data', [ClinicController::class, 'data'])->name('clinics.data');
        Route::get('clinics/{id}/users/data', [ClinicController::class, 'clinicUsersData'])->name('clinics.users.data');
        Route::put('clinics/{id}/update-status', [ClinicController::class, 'updateStatus'])->name('clinics.update-status');
        Route::put('clinics/{id}/update-is-allowed', [ClinicController::class, 'updateIsAllowed'])->name('clinics.update-is-allowed');
        Route::resource('clinics', ClinicController::class);

        Route::get('suppliers/data', [SupplierController::class, 'data'])->name('suppliers.data');
        Route::put('suppliers/{id}/update-status', [SupplierController::class, 'updateStatus'])->name('suppliers.update-status');
        Route::put('suppliers/{id}/update-is-allowed', [SupplierController::class, 'updateIsAllowed'])->name('suppliers.update-is-allowed');
        Route::resource('suppliers', SupplierController::class);

        // Rental Space Management
        Route::get('rental-spaces/data', [RentalSpaceController::class, 'data'])->name('rental-spaces.data');
        Route::get('rental-spaces/trash', [RentalSpaceController::class, 'trash'])->name('rental-spaces.trash');
        Route::get('rental-spaces/trash/data', [RentalSpaceController::class, 'trashData'])->name('rental-spaces.trash.data');
        Route::post('rental-spaces/{id}/restore', [RentalSpaceController::class, 'restore'])->name('rental-spaces.restore');
        Route::put('rental-spaces/{id}/update-status', [RentalSpaceController::class, 'updateStatus'])->name('rental-spaces.update-status');
        Route::delete('rental-spaces/{id}/force-delete', [RentalSpaceController::class, 'forceDelete'])->name('rental-spaces.force-delete');
        Route::resource('rental-spaces', RentalSpaceController::class);

        Route::get('approvements/{id}', [ModuleApprovementController::class, 'getApprovement'])->name('approvements.data');
        Route::post('approvements', [ModuleApprovementController::class, 'storeApprovement'])->name('approvements.store');
        Route::put('approvements/{id}', [ModuleApprovementController::class, 'updateApprovement'])->name('approvements.update');

         // Doctor Profiles Management
         Route::group(['prefix' => 'doctor-profiles'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'data'])->name('doctor-profiles.data');
            Route::get('/pending', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'pending'])->name('doctor-profiles.pending');
            Route::get('/pending/data', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'pendingData'])->name('doctor-profiles.pending.data');
            Route::post('/approve/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'approve'])->name('doctor-profiles.approve');
            Route::post('/reject/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'reject'])->name('doctor-profiles.reject');
            Route::post('/toggle-featured/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'toggleFeatured'])->name('doctor-profiles.toggle-featured');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'index'])->name('doctor-profiles.index');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\DoctorProfileController::class, 'show'])->name('doctor-profiles.show');
        });

        // Notifications Management
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'data'])->name('notifications.data');
            Route::get('/latest', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'getLatest'])->name('notifications.latest');
            Route::post('/mark-as-read/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
            Route::post('/mark-all-as-read', [\App\Http\Controllers\Backend\Dashboards\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        });

        // Blogs Management
        Route::get('blog-categories/data', [BlogCategoryController::class, 'data'])->name('blog-categories.data');
        Route::put('blog-categories/{id}/update-status', [BlogCategoryController::class, 'updateStatus'])->name('blog-categories.update-status');
        Route::get('blog-categories/trash', [BlogCategoryController::class, 'trash'])->name('blog-categories.trash');
        Route::get('blog-categories/trash/data', [BlogCategoryController::class, 'trashData'])->name('blog-categories.trash.data');
        Route::post('blog-categories/{id}/restore', [BlogCategoryController::class, 'restore'])->name('blog-categories.restore');
        Route::delete('blog-categories/{id}/force-delete', [BlogCategoryController::class, 'forceDelete'])->name('blog-categories.force-delete');
        Route::resource('blog-categories', BlogCategoryController::class);

        Route::get('blog-posts/data', [BlogPostController::class, 'data'])->name('blog-posts.data');
        Route::put('blog-posts/{id}/update-status', [BlogPostController::class, 'updateStatus'])->name('blog-posts.update-status');
        Route::get('blog-posts/trash', [BlogPostController::class, 'trash'])->name('blog-posts.trash');
        Route::get('blog-posts/trash/data', [BlogPostController::class, 'trashData'])->name('blog-posts.trash.data');
        Route::post('blog-posts/{id}/restore', [BlogPostController::class, 'restore'])->name('blog-posts.restore');
        Route::delete('blog-posts/{id}/force-delete', [BlogPostController::class, 'forceDelete'])->name('blog-posts.force-delete');
        Route::resource('blog-posts', BlogPostController::class);

        // Admin Users Management
        Route::group(['prefix' => 'admin-users'], function () {
            Route::get('/roles', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'roles'])->name('admin-users.roles');
            Route::get('/data', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'data'])->name('admin-users.data');
            Route::get('/trash', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'trash'])->name('admin-users.trash');
            Route::get('/trash/data', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'trashData'])->name('admin-users.trash.data');
            Route::post('/restore/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'restore'])->name('admin-users.restore');
            Route::delete('/force/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'forceDelete'])->name('admin-users.force.delete');
            Route::post('/toggle-status/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'toggleStatus'])->name('admin-users.toggle.status');
            Route::get('/', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'index'])->name('admin-users.index');
            Route::post('/', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'store'])->name('admin-users.store');
            Route::get('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'show'])->name('admin-users.show');
            Route::put('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'update'])->name('admin-users.update');
            Route::delete('/{id}', [\App\Http\Controllers\Backend\Dashboards\Admin\AdminUserController::class, 'destroy'])->name('admin-users.destroy');
        });

        // Courses Management
        Route::get('courses/data', [CourseController::class, 'data'])->name('courses.data');
        Route::put('courses/{id}/update-status', [CourseController::class, 'updateStatus'])->name('courses.update-status');
        Route::get('courses/trash', [CourseController::class, 'trash'])->name('courses.trash');
        Route::get('courses/trash/data', [CourseController::class, 'trashData'])->name('courses.trash.data');
        Route::post('courses/{id}/restore', [CourseController::class, 'restore'])->name('courses.restore');
        Route::delete('courses/{id}/force-delete', [CourseController::class, 'forceDelete'])->name('courses.force-delete');
        Route::resource('courses', CourseController::class);



    }
);


Route::post('/admin/logout', function (Request $request) {
    Auth::guard('admin')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to('/');
})->name('admin.logout');
