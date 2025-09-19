<?php

namespace App\Providers;

use App\Interfaces\Supplier\ProductRepositoryInterface;
use App\Repository\Supplier\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('App\Repository\Admin\CategoryRepositoryInterface', 'App\Repository\Admin\CategoryRepository');
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }


    public function boot()
    {
        //
    }
}
