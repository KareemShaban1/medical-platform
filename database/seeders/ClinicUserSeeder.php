<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClinicUser;
use Illuminate\Support\Facades\Hash;

class ClinicUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ClinicUser::create([
            'clinic_id' => 1,
            'name' => 'Clinic User 1',
            'email' => 'user@clinic1.com',
            'password' => Hash::make('password'),
            'status' => true,
            'salary_frequency' => 'monthly',
            'amount_per_salary_frequency' => 1000,
        ]);
    }
}