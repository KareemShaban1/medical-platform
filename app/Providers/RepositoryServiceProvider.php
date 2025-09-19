<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('App\Repository\Admin\CategoryRepositoryInterface', 'App\Repository\Admin\CategoryRepository');

        // Role Repositories
        $this->app->bind('App\Repository\Admin\RoleRepositoryInterface', 'App\Repository\Admin\RoleRepository');
        $this->app->bind('App\Repository\Supplier\RoleRepositoryInterface', 'App\Repository\Supplier\RoleRepository');
        $this->app->bind('App\Repository\Clinic\RoleRepositoryInterface', 'App\Repository\Clinic\RoleRepository');
    }


    public function boot()
    {
        //
    }
}
