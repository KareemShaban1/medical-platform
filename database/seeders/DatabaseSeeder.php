<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\SystemAdminSeeder;
use Database\Seeders\Guards\AdminRolePermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   MEDICAL PLATFORM DATABASE SEEDER');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();

        $startTime = microtime(true);

        // Step 1: Core Setup (Users, Roles, Clinics)
        $this->command->info('🏥 STEP 1: Setting up core data...');
        $this->command->info('───────────────────────────────────────────────────────────');

        $this->call([


            AdminRolePermissionSeeder::class,
            SystemAdminSeeder::class,

            // Core domain dictionaries
            SpecialitySeeder::class,          // Medical specialities
            CategorySeeder::class,            // Product categories


            ClinicSeeder::class,              // Clinics
            SupplierSeeder::class,            // Suppliers

            RoleAndPermissionSeeder::class,   // Create roles and permissions (guards/teams)

            // Users for suppliers and initial content
            //SupplierUserSeeder::class,        // Supplier users + roles
            ProductSeeder::class,             // Products
            BlogCategorySeeder::class,
            BlogPostSeeder::class,
            CourseSeeder::class,
            JobSeeder::class,
            RentalSpaceSeeder::class,

            // Optional geo/auxiliary seeds (safe even if empty)
            GovernorateSeeder::class,
            CitySeeder::class,
            AreaSeeder::class,
            SubscriptionSeeder::class,
        ]);


        $this->command->newLine();

        // Step 3: Working Hours & Availability
        $this->command->info('📅 STEP 2: Setting up working hours and availability...');
        $this->command->info('───────────────────────────────────────────────────────────');

        $this->call([
            WorkingHourSeeder::class,         // Create working hours and daily periods
        ]);

        $this->command->newLine();

        // Step 4: Patients
        $this->command->info('🧑‍🤝‍🧑 STEP 3: Creating patients and assignments...');
        $this->command->info('───────────────────────────────────────────────────────────');

        $this->call([
            PatientSeeder::class,             // Create patients and assign to doctors
        ]);

        $this->command->newLine();

        // Step 5: Appointments
        $this->command->info('📋 STEP 4: Creating appointments...');
        $this->command->info('───────────────────────────────────────────────────────────');

        $this->call([
            AppointmentSeeder::class,         // Create appointments
        ]);

        $this->command->newLine();

        // Step 6: Medical Records & Prescriptions
        $this->command->info('📄 STEP 5: Creating medical records and prescriptions...');
        $this->command->info('───────────────────────────────────────────────────────────');

        $this->call([
            MedicalRecordSeeder::class,       // Create medical records
            PrescriptionSeeder::class,        // Create prescriptions
        ]);

        $this->command->newLine();

        // Step 7: Lab Orders
        $this->command->info('🧪 STEP 6: Creating lab orders...');
        $this->command->info('───────────────────────────────────────────────────────────');

        $this->call([
            LabOrderSeeder::class,            // Create lab orders
        ]);

        $this->command->newLine();

        // Expenses categories and expenses
        $this->command->info('Seeding expense categories and expenses...');
        $this->call([
            ExpenseCategorySeeder::class,
            ExpenseSeeder::class,
        ]);

        $this->command->newLine();

    // Step 7: Invoices (based on completed appointments)
    $this->command->info('💳 STEP 7: Generating invoices...');
    $this->command->info('───────────────────────────────────────────────────────────');

        $this->call([
            InvoiceSeeder::class,
        ]);

        $this->command->newLine();

        // Final Summary
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   ✓ DATABASE SEEDING COMPLETED SUCCESSFULLY!');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();

        $this->displayFinalSummary($executionTime);
    }

    private function displayFinalSummary($executionTime)
    {
        $this->command->info('📊 FINAL STATISTICS:');
        $this->command->info('───────────────────────────────────────────────────────────');

        $stats = [
            'Clinics' => \App\Models\Clinic::count(),
            'Doctors' => \App\Models\DoctorProfile::count(),
            'Patients' => \App\Models\Patient::count(),
            'Appointments' => \App\Models\Appointment::count(),
            'Medical Records' => \App\Models\MedicalRecord::count(),
            'Prescriptions' => \App\Models\Prescription::count(),
            'Lab Orders' => \App\Models\LabOrder::count(),
        ];

        foreach ($stats as $label => $count) {
            $this->command->info(sprintf('   %-20s: %s', $label, number_format($count)));
        }

        $this->command->newLine();
        $this->command->info("⏱️  Execution Time: {$executionTime} seconds");
        $this->command->newLine();

        $this->command->info('🔑 DEFAULT CREDENTIALS:');
        $this->command->info('───────────────────────────────────────────────────────────');
        $this->command->info('   Super Admin:');
        $this->command->info('   Email: admin@medical.com');
        $this->command->info('   Password: password');
        $this->command->newLine();
        $this->command->info('   Clinic Admin (Clinic 1):');
        $this->command->info('   Email: admin.clinic1@medical.com');
        $this->command->info('   Password: password');
        $this->command->newLine();
        $this->command->info('   Doctor Examples:');
        $this->command->info('   Email: john.smith.clinic1@medical.com');
        $this->command->info('   Email: sarah.johnson.clinic1@medical.com');
        $this->command->info('   Password: password (for all)');
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
    }
}


