<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Observers\CategoryObserver;
use App\Models\RentalSpace;
use App\Observers\RentalSpaceObserver;
use App\Models\BlogCategory;
use App\Observers\BlogCategoryObserver;
use App\Models\BlogPost;
use App\Observers\BlogPostObserver;
use App\Models\Course;
use App\Observers\CourseObserver;
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
    }
}