<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Admin Policies
        \App\Models\Admin::class => \App\Policies\AdminPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\BlogCategory::class => \App\Policies\BlogCategoryPolicy::class,
        \App\Models\BlogPost::class => \App\Policies\BlogPostPolicy::class,
        \App\Models\Course::class => \App\Policies\CoursePolicy::class,
        \App\Models\Clinic::class => \App\Policies\ClinicPolicy::class,
        \App\Models\Supplier::class => \App\Policies\SupplierPolicy::class,
        \App\Models\Governorate::class => \App\Policies\GovernoratePolicy::class,
        \App\Models\City::class => \App\Policies\CityPolicy::class,
        \App\Models\Area::class => \App\Policies\AreaPolicy::class,

        // Clinic Policies
        \App\Models\ClinicUser::class => \App\Policies\ClinicUserPolicy::class,
        \App\Models\Job::class => \App\Policies\JobPolicy::class,
        \App\Models\ClinicInventory::class => \App\Policies\ClinicInventoryPolicy::class,
        \App\Models\ClinicUserSalary::class => \App\Policies\ClinicUserSalaryPolicy::class,
        \App\Models\SalaryContract::class => \App\Policies\SalaryContractPolicy::class,
        \App\Models\Payslip::class => \App\Policies\PayslipPolicy::class,
        \App\Models\ExpenseCategory::class => \App\Policies\ExpenseCategoryPolicy::class,
        \App\Models\Expense::class => \App\Policies\ExpensePolicy::class,
        \App\Models\Prescription::class => \App\Policies\PrescriptionPolicy::class,
        \App\Models\JobApplicationField::class => \App\Policies\JobApplicationFieldsPolicy::class,

        // Supplier Policies
        \App\Models\SupplierUser::class => \App\Policies\SupplierUserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register policies
        $this->registerPolicies();
    }
}











