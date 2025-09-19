<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('App\Repository\Admin\CategoryRepositoryInterface', 'App\Repository\Admin\CategoryRepository');
        $this->app->bind('App\Repository\Admin\ClinicRepositoryInterface', 'App\Repository\Admin\ClinicRepository');
        $this->app->bind('App\Repository\Admin\SupplierRepositoryInterface', 'App\Repository\Admin\SupplierRepository');

    }


    public function boot()
    {
        //
    }
}