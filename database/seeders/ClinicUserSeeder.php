<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClinicUser;
use App\Models\DoctorProfile;
use App\Models\Role;
use App\Models\Clinic;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class ClinicUserSeeder extends Seeder
{
    private $doctorData = [
        [
            'name' => 'Dr. John Smith',
            'email' => 'john.smith@medical.com',
            'specialty' => 'Cardiologist',
            'bio' => 'Highly experienced cardiologist with over 15 years of expertise in cardiovascular medicine.',
            'years_experience' => 15,
            'specialties' => ['Interventional Cardiology', 'Cardiac Catheterization', 'Heart Failure Management'],
            'services' => ['Cardiac Consultation', 'ECG Testing', 'Echocardiography', 'Angioplasty'],
        ],
        [
            'name' => 'Dr. Sarah Johnson',
            'email' => 'sarah.johnson@medical.com',
            'specialty' => 'Pediatrician',
            'bio' => 'Compassionate pediatrician with 12 years of experience in child healthcare.',
            'years_experience' => 12,
            'specialties' => ['General Pediatrics', 'Child Development', 'Preventive Care'],
            'services' => ['Well-Child Checkups', 'Vaccination', 'Growth Monitoring', 'Nutritional Counseling'],
        ],
        [
            'name' => 'Dr. Ahmed Hassan',
            'email' => 'ahmed.hassan@medical.com',
            'specialty' => 'Orthopedic Surgeon',
            'bio' => 'Distinguished orthopedic surgeon with 18 years of expertise in musculoskeletal medicine.',
            'years_experience' => 18,
            'specialties' => ['Orthopedic Surgery', 'Sports Medicine', 'Joint Replacement'],
            'services' => ['Orthopedic Consultation', 'Joint Replacement Surgery', 'Arthroscopic Surgery'],
        ],
        [
            'name' => 'Dr. Emily Chen',
            'email' => 'emily.chen@medical.com',
            'specialty' => 'Dermatologist',
            'bio' => 'Expert dermatologist specializing in medical and cosmetic dermatology.',
            'years_experience' => 10,
            'specialties' => ['Medical Dermatology', 'Cosmetic Dermatology', 'Skin Cancer Treatment'],
            'services' => ['Skin Consultation', 'Acne Treatment', 'Skin Cancer Screening', 'Cosmetic Procedures'],
        ],
        [
            'name' => 'Dr. Michael Brown',
            'email' => 'michael.brown@medical.com',
            'specialty' => 'General Physician',
            'bio' => 'Experienced general physician providing comprehensive primary care.',
            'years_experience' => 14,
            'specialties' => ['Primary Care', 'Preventive Medicine', 'Chronic Disease Management'],
            'services' => ['General Consultation', 'Health Screenings', 'Chronic Disease Care', 'Preventive Care'],
        ],
        [
            'name' => 'Dr. Fatima Al-Rashid',
            'email' => 'fatima.alrashid@medical.com',
            'specialty' => 'Gynecologist',
            'bio' => 'Dedicated gynecologist with expertise in women\'s health and reproductive medicine.',
            'years_experience' => 13,
            'specialties' => ['Obstetrics', 'Gynecology', 'Women\'s Health', 'Prenatal Care'],
            'services' => ['Prenatal Care', 'Women\'s Health Exams', 'Family Planning', 'Gynecological Surgery'],
        ],
    ];

    public function run(): void
    {
        $this->command->info('Creating clinic users and doctor profiles...');

        $clinics = Clinic::all();

        if ($clinics->isEmpty()) {
            $this->command->error('No clinics found! Please run ClinicSeeder first.');
            return;
        }

        foreach ($clinics as $clinicIndex => $clinic) {
            $this->command->info("\n--- Setting up {$clinic->name} ---");

            // Create clinic admin
            $this->createClinicAdmin($clinic, $clinicIndex);

            // Create 2-3 doctors per clinic
            $doctorsPerClinic = rand(2, 3);
            $this->createDoctorsForClinic($clinic, $clinicIndex, $doctorsPerClinic);
        }

        $this->command->info("\n✓ Successfully created all clinic users and doctor profiles");
    }

    private function createClinicAdmin($clinic, $index)
    {
        $admin = ClinicUser::create([
            'clinic_id' => $clinic->id,
            'name' => "Admin - {$clinic->name}",
            'email' => "admin.clinic{$clinic->id}@medical.com",
            'password' => Hash::make('password'),
            'phone' => '0100000' . str_pad($clinic->id, 4, '0', STR_PAD_LEFT),
            'status' => true,
            'position_title' => 'Clinic Administrator',
            'salary_frequency' => 'monthly',
            'amount_per_salary_frequency' => 3000,
        ]);

        $this->assignRole($admin, 'clinic-admin', $clinic->id);
        $this->command->info("  ✓ Created admin: {$admin->name}");
    }

    private function createDoctorsForClinic($clinic, $clinicIndex, $count)
    {
        // Select random doctors from our data
        $selectedDoctors = collect($this->doctorData)->random($count);

        foreach ($selectedDoctors as $doctorIndex => $doctorInfo) {
            $uniqueEmail = str_replace('@medical.com', ".clinic{$clinic->id}@medical.com", $doctorInfo['email']);

            $doctor = ClinicUser::create([
                'clinic_id' => $clinic->id,
                'name' => $doctorInfo['name'],
                'email' => $uniqueEmail,
                'password' => Hash::make('password'),
                'phone' => '0120' . str_pad($clinic->id . $doctorIndex, 6, '0', STR_PAD_LEFT),
                'status' => true,
                'position_title' => $doctorInfo['specialty'],
                'salary_frequency' => 'monthly',
                'amount_per_salary_frequency' => rand(4000, 6000),
            ]);

            $this->assignRole($doctor, 'doctor', $clinic->id);
            $this->createDoctorProfile($doctor, $doctorInfo);

            $this->command->info("  ✓ Created doctor: {$doctor->name} ({$doctorInfo['specialty']})");
        }
    }

    private function createDoctorProfile($clinicUser, $doctorInfo)
    {
        $profile = DoctorProfile::create([
            'clinic_user_id' => $clinicUser->id,
            'speciality_id' => rand(1, 10), // Assuming specialities exist
            'name' => $clinicUser->name,
            'bio' => $doctorInfo['bio'],
            'email' => $clinicUser->email,
            'phone' => $clinicUser->phone,
            'twitter_link' => 'https://twitter.com/' . str_replace([' ', '.'], '', strtolower($clinicUser->name)),
            'linkedin_link' => 'https://linkedin.com/in/' . str_replace([' ', '.'], '', strtolower($clinicUser->name)),
            'years_experience' => $doctorInfo['years_experience'],
            'specialties' => $doctorInfo['specialties'],
            'services_offered' => $doctorInfo['services'],
            'education' => $this->generateEducation($doctorInfo['specialty']),
            'experience' => $this->generateExperience($clinicUser->name, $doctorInfo['specialty']),
            'status' => DoctorProfile::STATUS_APPROVED,
            'reviewed_at' => now(),
            'reviewed_by' => 1,
            'is_featured' => rand(0, 100) > 70, // 30% chance of being featured
        ]);

        $this->addProfileImage($profile, $clinicUser->name);
    }

    private function generateEducation($specialty)
    {
        return [
            [
                'degree' => 'MD - Doctor of Medicine',
                'institution' => 'Cairo University Faculty of Medicine',
                'year' => (string)(date('Y') - rand(15, 25)),
                'location' => 'Cairo, Egypt',
            ],
            [
                'degree' => "Specialization in {$specialty}",
                'institution' => 'Harvard Medical School',
                'year' => (string)(date('Y') - rand(10, 15)),
                'location' => 'Boston, MA, USA',
            ],
        ];
    }

    private function generateExperience($name, $specialty)
    {
        return [
            [
                'position' => "Senior {$specialty}",
                'institution' => 'Current Clinic',
                'start_date' => (string)(date('Y') - rand(3, 8)),
                'end_date' => 'Present',
                'description' => "Providing expert {$specialty} care and leading department initiatives.",
            ],
        ];
    }

    private function assignRole($user, $roleName, $clinicId)
    {
        try {
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($clinicId);
            }

            $role = Role::where('name', $roleName)
                ->where('guard_name', 'clinic')
                ->where('team_id', $clinicId)
                ->first();

            if ($role) {
                $user->assignRole($role);
            }
        } catch (\Exception $e) {
            $this->command->warn("Could not assign role: " . $e->getMessage());
        }
    }

    private function addProfileImage($profile, $name)
    {
        try {
            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=512&background=667eea&color=fff';
            $imageContent = Http::get($avatarUrl)->body();
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.png';
            file_put_contents($tempPath, $imageContent);

            $profile->addMedia($tempPath)
                ->usingName($name)
                ->toMediaCollection('profile_photo');

            @unlink($tempPath);
        } catch (\Exception $e) {
            $this->command->warn("Could not add profile image: " . $e->getMessage());
        }
    }
}
