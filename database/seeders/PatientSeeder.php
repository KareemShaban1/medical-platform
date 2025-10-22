<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating patients...');

        $patients = [
            [
                'name' => 'Ahmed Mohamed Ali',
                'email' => 'ahmed.mohamed@example.com',
                'phone' => '01012345678',

            ],
            [
                'name' => 'Fatima Hassan Ibrahim',
                'email' => 'fatima.hassan@example.com',
                'phone' => '01023456789',

            ],
            [
                'name' => 'Omar Khaled Said',
                'email' => 'omar.khaled@example.com',
                'phone' => '01034567890',

            ],
            [
                'name' => 'Mona Samy Mahmoud',
                'email' => 'mona.samy@example.com',
                'phone' => '01045678901',

            ],
            [
                'name' => 'Youssef Tarek Ahmed',
                'email' => 'youssef.tarek@example.com',
                'phone' => '01056789012',

            ],
            [
                'name' => 'Nour Adel Hossam',
                'email' => 'nour.adel@example.com',
                'phone' => '01067890123',

            ],
            [
                'name' => 'Karim Essam Fathy',
                'email' => 'karim.essam@example.com',
                'phone' => '01078901234',
            ],
            [
                'name' => 'Sara Mohamed Yasser',
                'email' => 'sara.mohamed@example.com',
                'phone' => '01089012345',
            ],
            [
                'name' => 'Hassan Ali Mahmoud',
                'email' => 'hassan.ali@example.com',
                'phone' => '01090123456',
            ],
            [
                'name' => 'Layla Ibrahim Hany',
                'email' => 'layla.ibrahim@example.com',
                'phone' => '01001234567',
            ],
        ];

        foreach ($patients as $patientData) {
            // Create user account
            $user = User::create([
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'password' => Hash::make('password'),
            ]);

            // Create patient profile
            Patient::create([
                'user_id' => $user->id,
                'phone' => $patientData['phone'],
            ]);

            $this->command->info("Created patient: {$patientData['name']}");
        }

        $this->command->info('Patients created successfully!');
    }
}

