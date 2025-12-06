<?php

namespace App\Providers;

use App\Models\Job;
use App\Models\Course;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\RentalSpace;
use App\Models\BlogCategory;
use App\Observers\JobObserver;
use App\Models\ClinicInventory;
use App\Observers\CourseObserver;
use App\Observers\BlogPostObserver;
use App\Observers\CategoryObserver;
use Illuminate\Pagination\Paginator;
use App\Observers\RentalSpaceObserver;
use App\Observers\BlogCategoryObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\ClinicInventoryObserver;
use App\Models\ExpenseCategory;
use App\Observers\ExpenseCategoryObserver;
use App\Models\Expense;
use App\Observers\ExpenseObserver;
use App\PaymentGateways\PaymentGatewayManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class, function ($app) {
            return new PaymentGatewayManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        // add observer to category model
        Category::observe(CategoryObserver::class);
        RentalSpace::observe(RentalSpaceObserver::class);
        BlogCategory::observe(BlogCategoryObserver::class);
        BlogPost::observe(BlogPostObserver::class);
        Course::observe(CourseObserver::class);
        Job::observe(JobObserver::class);
        ClinicInventory::observe(ClinicInventoryObserver::class);
        ExpenseCategory::observe(ExpenseCategoryObserver::class);
        Expense::observe(ExpenseObserver::class);

        // Register Blade directives for permissions
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives for permissions
     */
    protected function registerBladeDirectives(): void
    {
        // @hasPermission directive - uses the helper function to avoid code duplication
        Blade::if('hasPermission', function ($permission) {
            return hasPermission($permission);
        });

        // @hasRole directive - uses helper function
        Blade::if('hasRole', function ($role) {
            return hasRole($role);
        });

        // @hasAnyRole directive - uses helper function
        Blade::if('hasAnyRole', function (...$roles) {
            return hasAnyRole(...$roles);
        });

        // @hasAllRoles directive - uses helper function
        Blade::if('hasAllRoles', function (...$roles) {
            return hasAllRoles(...$roles);
        });

        // @hasAnyPermission directive - check if user has ANY of the given permissions
        Blade::if('hasAnyPermission', function (...$permissions) {
            return hasAnyPermission(...$permissions);
        });

        // @hasAllPermissions directive - check if user has ALL of the given permissions
        Blade::if('hasAllPermissions', function (...$permissions) {
            return hasAllPermissions(...$permissions);
        });
    }
}
