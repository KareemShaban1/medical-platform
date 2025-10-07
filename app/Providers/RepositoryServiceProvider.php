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
        $this->app->bind('App\Interfaces\Admin\RentalSpaceRepositoryInterface', 'App\Repository\Admin\RentalSpaceRepository');
        $this->app->bind('App\Interfaces\Admin\ModuleApprovementRepositoryInterface', 'App\Repository\Admin\ModuleApprovementRepository');
        $this->app->bind('App\Interfaces\Admin\BlogCategoryRepositoryInterface', 'App\Repository\Admin\BlogCategoryRepository');
        $this->app->bind('App\Interfaces\Admin\BlogPostRepositoryInterface', 'App\Repository\Admin\BlogPostRepository');
        $this->app->bind('App\Interfaces\Admin\CourseRepositoryInterface', 'App\Repository\Admin\CourseRepository');
        $this->app->bind('App\Interfaces\Admin\AdminUserRepositoryInterface', 'App\Repository\Admin\AdminUserRepository');
        $this->app->bind('App\Interfaces\Admin\JobRepositoryInterface', 'App\Repository\Admin\JobRepository');

        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

        // User Repositories
        $this->app->bind('App\Interfaces\Supplier\UserRepositoryInterface', 'App\Repository\Supplier\UserRepository');
        $this->app->bind('App\Interfaces\Clinic\UserRepositoryInterface', 'App\Repository\Clinic\UserRepository');

        // Role Repositories
        $this->app->bind('App\Interfaces\Admin\RoleRepositoryInterface', 'App\Repository\Admin\RoleRepository');
        $this->app->bind('App\Interfaces\Supplier\RoleRepositoryInterface', 'App\Repository\Supplier\RoleRepository');
        $this->app->bind('App\Interfaces\Clinic\RoleRepositoryInterface', 'App\Repository\Clinic\RoleRepository');

        // Doctor Profile Repositories
        $this->app->bind('App\Interfaces\Clinic\DoctorProfileRepositoryInterface', 'App\Repository\Clinic\DoctorProfileRepository');
        $this->app->bind('App\Interfaces\Admin\DoctorProfileRepositoryInterface', 'App\Repository\Admin\DoctorProfileRepository');

        // Patient Repository
        $this->app->bind('App\Interfaces\Clinic\PatientRepositoryInterface', 'App\Repository\Clinic\PatientRepository');
        // rental space repository
        $this->app->bind('App\Interfaces\Clinic\RentalSpaceRepositoryInterface', 'App\Repository\Clinic\RentalSpaceRepository');
        // job repository
        $this->app->bind('App\Interfaces\Clinic\JobRepositoryInterface', 'App\Repository\Clinic\JobRepository');
        // supplier product repository
        $this->app->bind('App\Interfaces\Admin\SupplierProductRepositoryInterface', 'App\Repository\Admin\SupplierProductRepository');

        // Tickets System Repositories
        $this->app->bind('App\Interfaces\Clinic\RequestRepositoryInterface', 'App\Repository\Clinic\RequestRepository');
        $this->app->bind('App\Interfaces\Supplier\OfferRepositoryInterface', 'App\Repository\Supplier\OfferRepository');
        $this->app->bind('App\Interfaces\Supplier\SpecializedCategoryRepositoryInterface', 'App\Repository\Supplier\SpecializedCategoryRepository');

        // Job Application Field Repository
        $this->app->bind('App\Interfaces\Clinic\JobApplicationFieldRepositoryInterface', 'App\Repository\Clinic\JobApplicationFieldRepository');

        // Ticket Repositories
        $this->app->bind('App\Interfaces\Admin\TicketRepositoryInterface', 'App\Repository\Admin\TicketRepository');
        $this->app->bind('App\Interfaces\User\TicketRepositoryInterface', 'App\Repository\User\TicketRepository');
    }



    public function boot()
    {
        //
    }
}
