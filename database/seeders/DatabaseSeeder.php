<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            AdminSeeder::class,
            ClinicSeeder::class,
            SupplierSeeder::class,
            RoleAndPermissionSeeder::class,
            ClinicUserSeeder::class,
            SupplierUserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        $this->call([
           
            BlogCategorySeeder::class,
            BlogPostSeeder::class,
            CourseSeeder::class,
            JobSeeder::class,
            RentalSpaceSeeder::class,
        ]);
    }
}
