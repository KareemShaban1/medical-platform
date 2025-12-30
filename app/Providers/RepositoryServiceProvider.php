<?php



namespace App\Providers;



use App\Interfaces\Supplier\ProductRepositoryInterface;

use App\Repository\Supplier\ProductRepository;

use App\Interfaces\Clinic\LabOrderRepositoryInterface as ClinicLabOrderRepositoryInterface;

use App\Repository\Clinic\LabOrderRepository as ClinicLabOrderRepository;

use App\Interfaces\User\LabOrderRepositoryInterface as UserLabOrderRepositoryInterface;

use App\Repository\User\LabOrderRepository as UserLabOrderRepository;
use App\Interfaces\Clinic\InvoiceRepositoryInterface;
use App\Repository\Clinic\InvoiceRepository;

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

        $this->app->bind('App\Interfaces\Admin\GovernorateRepositoryInterface', 'App\Repository\Admin\GovernorateRepository');

        $this->app->bind('App\Interfaces\Admin\CityRepositoryInterface', 'App\Repository\Admin\CityRepository');

        $this->app->bind('App\Interfaces\Admin\AreaRepositoryInterface', 'App\Repository\Admin\AreaRepository');

        $this->app->bind('App\Interfaces\Admin\UsersManagementRepositoryInterface', 'App\Repository\Admin\UsersManagementRepository');

        $this->app->bind('App\Interfaces\Admin\TranslationRepositoryInterface', 'App\Repository\Admin\TranslationRepository');



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

        $this->app->bind('App\\Interfaces\\Clinic\\WorkingHourRepositoryInterface', 'App\\Repository\\Clinic\\WorkingHourRepository');

        $this->app->bind('App\\Interfaces\\Clinic\\AttendanceRepositoryInterface', 'App\\Repository\\Clinic\\AttendanceRepository');

        // Expense Category Repository

        $this->app->bind('App\Interfaces\Clinic\ExpenseCategoryRepositoryInterface', 'App\Repository\Clinic\ExpenseCategoryRepository');



    $this->app->bind('App\Interfaces\Clinic\ExpenseRepositoryInterface', 'App\Repository\Clinic\ExpenseRepository');



        // Patient Repository

        $this->app->bind('App\Interfaces\Clinic\PatientRepositoryInterface', 'App\Repository\Clinic\PatientRepository');

        // rental space repository

        $this->app->bind('App\Interfaces\Clinic\RentalSpaceRepositoryInterface', 'App\Repository\Clinic\RentalSpaceRepository');

        // job repository

        $this->app->bind('App\Interfaces\Clinic\JobRepositoryInterface', 'App\Repository\Clinic\JobRepository');

        // supplier product repository

        $this->app->bind('App\Interfaces\Admin\SupplierProductRepositoryInterface', 'App\Repository\Admin\SupplierProductRepository');



        // Order Repositories

        $this->app->bind('App\Interfaces\Supplier\OrderRepositoryInterface', 'App\Repository\Supplier\OrderRepository');

        $this->app->bind('App\Interfaces\Admin\OrderRepositoryInterface', 'App\Repository\Admin\OrderRepository');

        // Announcements
        $this->app->bind('App\\Interfaces\\Admin\\AnnouncementRepositoryInterface', 'App\\Repository\\Admin\\AnnouncementRepository');

        // Banners
        $this->app->bind('App\\Interfaces\\Admin\\BannerRepositoryInterface', 'App\\Repository\\Admin\\BannerRepository');


        // Tickets System Repositories

        $this->app->bind('App\Interfaces\Clinic\RequestRepositoryInterface', 'App\Repository\Clinic\RequestRepository');

        $this->app->bind('App\Interfaces\Supplier\OfferRepositoryInterface', 'App\Repository\Supplier\OfferRepository');

        $this->app->bind('App\Interfaces\Supplier\SpecializedCategoryRepositoryInterface', 'App\Repository\Supplier\SpecializedCategoryRepository');



        // Job Application Field Repository

        $this->app->bind('App\Interfaces\Clinic\JobApplicationFieldRepositoryInterface', 'App\Repository\Clinic\JobApplicationFieldRepository');

        // Clinic Inventory Repository

        $this->app->bind('App\Interfaces\Clinic\ClinicInventoryRepositoryInterface', 'App\Repository\Clinic\ClinicInventoryRepository');

        // Clinic Inventory Movement Repository

        $this->app->bind('App\Interfaces\Clinic\ClinicInventoryMovementRepositoryInterface', 'App\Repository\Clinic\ClinicInventoryMovementRepository');



        // Clinic User Salary Repository

        $this->app->bind('App\Interfaces\Clinic\ClinicUserSalaryRepositoryInterface', 'App\Repository\Clinic\ClinicUserSalaryRepository');



        // salary contract repository

        $this->app->bind('App\Interfaces\Clinic\SalaryContractRepositoryInterface', 'App\Repository\Clinic\SalaryContractRepository');



			// payslip repository

			$this->app->bind('App\Interfaces\Clinic\PayslipRepositoryInterface', 'App\Repository\Clinic\PayslipRepository');



        // Ticket Repositories

        $this->app->bind('App\Interfaces\Admin\TicketRepositoryInterface', 'App\Repository\Admin\TicketRepository');

        $this->app->bind('App\Interfaces\User\TicketRepositoryInterface', 'App\Repository\User\TicketRepository');



        // Appointment System Repositories

        $this->app->bind('App\Interfaces\Clinic\AvailabilityOverrideRepositoryInterface', 'App\Repository\Clinic\AvailabilityOverrideRepository');

        $this->app->bind('App\Interfaces\Clinic\DailyPeriodRepositoryInterface', 'App\Repository\Clinic\DailyPeriodRepository');

        $this->app->bind('App\Interfaces\Clinic\AppointmentRepositoryInterface', 'App\Repository\Clinic\AppointmentRepository');

        $this->app->bind('App\Interfaces\User\DoctorProfileRepositoryInterface', 'App\Repository\User\DoctorProfileRepository');



        // Prescription Repository

        $this->app->bind('App\Interfaces\Clinic\PrescriptionRepositoryInterface', 'App\Repository\Clinic\PrescriptionRepository');



        // Lab Orders Repositories

        $this->app->bind(ClinicLabOrderRepositoryInterface::class, ClinicLabOrderRepository::class);

        $this->app->bind(UserLabOrderRepositoryInterface::class, UserLabOrderRepository::class);

        // Invoices Repository (Clinic)
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);



        // Medical Records

        $this->app->bind('App\\Interfaces\\Clinic\\MedicalRecordRepositoryInterface', 'App\\Repository\\Clinic\\MedicalRecordRepository');

    }







    public function boot()

    {

        //

    }

}



