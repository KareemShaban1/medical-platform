<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create system admin if it doesn't exist
        Admin::firstOrCreate(
            ['email' => 'system@medical-platform.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('system123456'),
                'status' => true,
            ]
        );
    }
}
