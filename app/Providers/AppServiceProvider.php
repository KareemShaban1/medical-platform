<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Observers\CategoryObserver;
use App\Models\RentalSpace;
use App\Observers\RentalSpaceObserver;

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
    }
}