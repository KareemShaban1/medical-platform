<?php

namespace App\Providers;

use App\Interfaces\Supplier\ProductRepositoryInterface;
use App\Repository\Supplier\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('App\Interfaces\Admin\CategoryRepositoryInterface', 'App\Repository\Admin\CategoryRepository');
        $this->app->bind('App\Interfaces\Admin\ClinicRepositoryInterface', 'App\Repository\Admin\ClinicRepository');
        $this->app->bind('App\Interfaces\Admin\SupplierRepositoryInterface', 'App\Repository\Admin\SupplierRepository');

        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        // Role Repositories
        $this->app->bind('App\Interfaces\Admin\RoleRepositoryInterface', 'App\Repository\Admin\RoleRepository');
        $this->app->bind('App\Interfaces\Supplier\RoleRepositoryInterface', 'App\Repository\Supplier\RoleRepository');
        $this->app->bind('App\Interfaces\Clinic\RoleRepositoryInterface', 'App\Repository\Clinic\RoleRepository');
    }


    public function boot()
    {
        //
    }
}

