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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
        Paginator::useBootstrapFour();

    }
}
