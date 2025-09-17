<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('App\Repository\Admin\CategoryRepositoryInterface', 'App\Repository\Admin\CategoryRepository');

    }


    public function boot()
    {
        //
    }
}