<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Clinic;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    private $firstNames = [
        'Ahmed', 'Mohamed', 'Ali', 'Omar', 'Khaled', 'Youssef', 'Hassan', 'Ibrahim',
        'Fatima', 'Aisha', 'Sara', 'Mariam', 'Nour', 'Hana', 'Layla', 'Zainab',
        'John', 'Michael', 'David', 'Sarah', 'Emily', 'Jessica', 'Daniel', 'James'
    ];

    private $lastNames = [
        'Hassan', 'Ali', 'Ahmed', 'Mohamed', 'Ibrahim', 'Mahmoud', 'Khalil', 'Mansour',
        'Smith', 'Johnson', 'Brown', 'Davis', 'Wilson', 'Moore', 'Taylor', 'Anderson'
    ];

    public function run(): void
    {
        $this->command->info('Creating patients and assigning to doctors...');

        $clinics = Clinic::with('clinicUsers.doctorProfile')->get();

        if ($clinics->isEmpty()) {
            $this->command->error('No clinics found! Please run ClinicSeeder first.');
            return;
        }

        $totalPatients = 0;

        foreach ($clinics as $clinic) {
            $doctors = $clinic->clinicUsers()
                ->whereHas('doctorProfile')
                ->with('doctorProfile')
                ->get();

            if ($doctors->isEmpty()) {
                $this->command->warn("No doctors found for {$clinic->name}, skipping...");
                continue;
            }

            $this->command->info("\n--- Creating patients for {$clinic->name} ---");

            // Create 15-25 patients per clinic
            $patientsCount = rand(15, 25);

            for ($i = 0; $i < $patientsCount; $i++) {
                $patient = $this->createPatient($clinic->id, $i);

                if ($patient) {
                    // Assign patient to 1-2 doctors in this clinic
                    $doctorsToAssign = $doctors->random(rand(1, min(2, $doctors->count())));

                    foreach ($doctorsToAssign as $doctor) {
                        if ($doctor->doctorProfile) {
                            $patient->doctors()->attach($doctor->doctorProfile->id, [
                                'clinic_id' => $clinic->id,
                                'assigned_by' => $doctor->id,
                                'assigned_at' => now()->subDays(rand(0, 90)),
                            ]);
                        }
                    }

                    $totalPatients++;
                }
            }

            $this->command->info("  ✓ Created {$patientsCount} patients for {$clinic->name}");
        }

        $this->command->info("\n✓ Successfully created {$totalPatients} patients across all clinics");
    }

    private function createPatient($clinicId, $index)
    {
        $firstName = $this->firstNames[array_rand($this->firstNames)];
        $lastName = $this->lastNames[array_rand($this->lastNames)];
        $fullName = "{$firstName} {$lastName}";
        $email = strtolower(str_replace(' ', '.', $fullName)) . ".c{$clinicId}.{$index}@patient.com";
        $phone = '0155' . str_pad(($clinicId * 1000) + $index, 7, '0', STR_PAD_LEFT);

        try {
            // Create user
            $user = User::create([
                'name' => $fullName,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);

            // Create patient
            $patient = Patient::create([
                'user_id' => $user->id,
                'phone' => $phone,
            ]);

            return $patient;
        } catch (\Exception $e) {
            $this->command->warn("  Could not create patient: " . $e->getMessage());
            return null;
        }
    }
}
