<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClinicUser;
use App\Models\DoctorProfile;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ClinicUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create clinic admin user
        $clinicAdmin = ClinicUser::create([
            'clinic_id' => 1,
            'name' => 'Clinic Admin',
            'email' => 'admin@clinic1.com',
            'password' => Hash::make('password'),
            'phone' => '01034113921',
            'status' => true,
            'salary_frequency' => 'monthly',
            'amount_per_salary_frequency' => 2000,
        ]);

        // Create doctor users
        $doctor1 = ClinicUser::create([
            'clinic_id' => 1,
            'name' => 'Dr. John Smith',
            'email' => 'doctor1@clinic1.com',
            'password' => Hash::make('password'),
            'status' => true,
            'phone' => '01264313921',
            'position_title' => 'Senior Cardiologist',
            'salary_frequency' => 'monthly',
            'amount_per_salary_frequency' => 5000,
        ]);

        $doctor2 = ClinicUser::create([
            'clinic_id' => 1,
            'name' => 'Dr. Sarah Johnson',
            'email' => 'doctor2@clinic1.com',
            'password' => Hash::make('password'),
            'status' => true,
            'phone' => '01264313922',
            'position_title' => 'Pediatrician',
            'salary_frequency' => 'monthly',
            'amount_per_salary_frequency' => 4500,
        ]);

        $doctor3 = ClinicUser::create([
            'clinic_id' => 1,
            'name' => 'Dr. Ahmed Hassan',
            'email' => 'doctor3@clinic1.com',
            'password' => Hash::make('password'),
            'status' => true,
            'phone' => '01264313923',
            'position_title' => 'Orthopedic Surgeon',
            'salary_frequency' => 'monthly',
            'amount_per_salary_frequency' => 5500,
        ]);

        // Assign roles after user creation
        $this->assignRoles($clinicAdmin, [$doctor1, $doctor2, $doctor3]);

        // Create doctor profiles
        $this->createDoctorProfiles([$doctor1, $doctor2, $doctor3]);
    }

    /**
     * Assign roles to clinic users
     */
    private function assignRoles($clinicAdmin, $doctors)
    {
        try {
            // Set team context for the clinic
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId(1); // First clinic ID
            }

            // Assign clinic-admin role
            $clinicAdminRole = Role::where('name', 'clinic-admin')
                ->where('guard_name', 'clinic')
                ->where('team_id', 1)
                ->first();

            if ($clinicAdminRole) {
                $clinicAdmin->assignRole($clinicAdminRole);
                $this->command->info('Assigned clinic-admin role to: ' . $clinicAdmin->name);
            } else {
                $this->command->warn('clinic-admin role not found. Make sure RoleAndPermissionSeeder runs before this seeder.');
            }

            // Assign doctor role
            $doctorRole = Role::where('name', 'doctor')
                ->where('guard_name', 'clinic')
                ->where('team_id', 1)
                ->first();

            if ($doctorRole) {
                foreach ($doctors as $doctor) {
                    $doctor->assignRole($doctorRole);
                    $this->command->info('Assigned doctor role to: ' . $doctor->name);
                }
            } else {
                $this->command->warn('doctor role not found. Make sure RoleAndPermissionSeeder runs before this seeder.');
            }

        } catch (\Exception $e) {
            $this->command->error('Error assigning roles: ' . $e->getMessage());
        }
    }

    /**
     * Create doctor profiles for doctors
     */
    private function createDoctorProfiles($doctors)
    {
        try {
            // Doctor 1: Cardiologist
            $profile1 = DoctorProfile::create([
                'clinic_user_id' => $doctors[0]->id,
                'name' => $doctors[0]->name,
                'bio' => 'Dr. John Smith is a highly experienced cardiologist with over 15 years of expertise in cardiovascular medicine. He specializes in interventional cardiology and has performed over 2000 successful cardiac procedures. Dr. Smith is dedicated to providing comprehensive cardiac care and promoting heart health through preventive medicine.',
                'email' => $doctors[0]->email,
                'phone' => $doctors[0]->phone,
                'twitter_link' => 'https://twitter.com/drjohnsmith',
                'linkedin_link' => 'https://linkedin.com/in/drjohnsmith',
                'facebook_link' => 'https://facebook.com/drjohnsmith',
                'instagram_link' => 'https://instagram.com/drjohnsmith',
                'research_links' => [
                    'https://scholar.google.com/drjohnsmith',
                    'https://researchgate.net/profile/John-Smith',
                    'https://pubmed.ncbi.nlm.nih.gov/drjohnsmith',
                ],
                'years_experience' => 15,
                'specialties' => [
                    'Interventional Cardiology',
                    'Cardiac Catheterization',
                    'Coronary Artery Disease',
                    'Heart Failure Management',
                    'Preventive Cardiology',
                ],
                'services_offered' => [
                    'Cardiac Consultation',
                    'ECG & Stress Testing',
                    'Echocardiography',
                    'Cardiac Catheterization',
                    'Angioplasty & Stenting',
                    'Heart Disease Prevention Programs',
                ],
                'education' => [
                    [
                        'degree' => 'MD - Doctor of Medicine',
                        'institution' => 'Harvard Medical School',
                        'year' => '2005',
                        'location' => 'Boston, MA, USA',
                    ],
                    [
                        'degree' => 'Fellowship in Cardiology',
                        'institution' => 'Johns Hopkins Hospital',
                        'year' => '2008',
                        'location' => 'Baltimore, MD, USA',
                    ],
                    [
                        'degree' => 'Board Certification in Interventional Cardiology',
                        'institution' => 'American Board of Internal Medicine',
                        'year' => '2010',
                        'location' => 'USA',
                    ],
                ],
                'experience' => [
                    [
                        'position' => 'Senior Cardiologist',
                        'institution' => 'Medical Excellence Clinic',
                        'start_date' => '2015',
                        'end_date' => 'Present',
                        'description' => 'Leading the cardiology department and performing advanced cardiac procedures.',
                    ],
                    [
                        'position' => 'Attending Cardiologist',
                        'institution' => 'University Medical Center',
                        'start_date' => '2010',
                        'end_date' => '2015',
                        'description' => 'Provided comprehensive cardiac care and mentored cardiology fellows.',
                    ],
                ],
                'status' => DoctorProfile::STATUS_APPROVED,
                'reviewed_at' => now(),
                'is_featured' => true,
            ]);

            // Doctor 2: Pediatrician
            $profile2 = DoctorProfile::create([
                'clinic_user_id' => $doctors[1]->id,
                'name' => $doctors[1]->name,
                'bio' => 'Dr. Sarah Johnson is a compassionate pediatrician with 12 years of experience in child healthcare. She is passionate about child development and preventive care. Dr. Johnson has expertise in managing childhood illnesses, developmental assessments, and providing guidance to parents on nutrition and child wellness.',
                'email' => $doctors[1]->email,
                'phone' => $doctors[1]->phone,
                'twitter_link' => 'https://twitter.com/drsarahjohnson',
                'linkedin_link' => 'https://linkedin.com/in/drsarahjohnson',
                'facebook_link' => 'https://facebook.com/drsarahjohnson',
                'instagram_link' => 'https://instagram.com/drsarahjohnson',
                'research_links' => [
                    'https://scholar.google.com/drsarahjohnson',
                    'https://researchgate.net/profile/Sarah-Johnson',
                ],
                'years_experience' => 12,
                'specialties' => [
                    'General Pediatrics',
                    'Child Development',
                    'Preventive Care',
                    'Childhood Nutrition',
                    'Vaccination Programs',
                ],
                'services_offered' => [
                    'Well-Child Checkups',
                    'Vaccination & Immunization',
                    'Growth & Development Monitoring',
                    'Treatment of Childhood Illnesses',
                    'Nutritional Counseling',
                    'Parenting Guidance',
                ],
                'education' => [
                    [
                        'degree' => 'MD - Doctor of Medicine',
                        'institution' => 'Stanford University School of Medicine',
                        'year' => '2008',
                        'location' => 'Stanford, CA, USA',
                    ],
                    [
                        'degree' => 'Pediatric Residency',
                        'institution' => "Boston Children's Hospital",
                        'year' => '2011',
                        'location' => 'Boston, MA, USA',
                    ],
                    [
                        'degree' => 'Board Certification in Pediatrics',
                        'institution' => 'American Board of Pediatrics',
                        'year' => '2012',
                        'location' => 'USA',
                    ],
                ],
                'experience' => [
                    [
                        'position' => 'Pediatrician',
                        'institution' => 'Medical Excellence Clinic',
                        'start_date' => '2016',
                        'end_date' => 'Present',
                        'description' => 'Providing comprehensive pediatric care and leading child wellness programs.',
                    ],
                    [
                        'position' => 'Associate Pediatrician',
                        'institution' => 'City Children\'s Hospital',
                        'start_date' => '2012',
                        'end_date' => '2016',
                        'description' => 'Treated a wide range of pediatric conditions and participated in community health initiatives.',
                    ],
                ],
                'status' => DoctorProfile::STATUS_APPROVED,
                'reviewed_at' => now(),
                'is_featured' => true,
            ]);

            // Doctor 3: Orthopedic Surgeon
            $profile3 = DoctorProfile::create([
                'clinic_user_id' => $doctors[2]->id,
                'name' => $doctors[2]->name,
                'bio' => 'Dr. Ahmed Hassan is a distinguished orthopedic surgeon with 18 years of expertise in musculoskeletal medicine. He specializes in sports medicine, joint replacement surgery, and arthroscopic procedures. Dr. Hassan has performed over 3000 successful orthopedic surgeries and is known for his patient-centered approach to care.',
                'email' => $doctors[2]->email,
                'phone' => $doctors[2]->phone,
                'twitter_link' => 'https://twitter.com/drahmedhassan',
                'linkedin_link' => 'https://linkedin.com/in/drahmedhassan',
                'facebook_link' => 'https://facebook.com/drahmedhassan',
                'instagram_link' => 'https://instagram.com/drahmedhassan',
                'research_links' => [
                    'https://scholar.google.com/drahmedhassan',
                    'https://researchgate.net/profile/Ahmed-Hassan',
                    'https://pubmed.ncbi.nlm.nih.gov/drahmedhassan',
                ],
                'years_experience' => 18,
                'specialties' => [
                    'Orthopedic Surgery',
                    'Sports Medicine',
                    'Joint Replacement',
                    'Arthroscopic Surgery',
                    'Trauma Surgery',
                    'Spine Surgery',
                ],
                'services_offered' => [
                    'Orthopedic Consultation',
                    'Joint Replacement Surgery (Hip, Knee, Shoulder)',
                    'Arthroscopic Surgery',
                    'Sports Injury Treatment',
                    'Fracture Management',
                    'Spine Surgery',
                    'Rehabilitation Programs',
                ],
                'education' => [
                    [
                        'degree' => 'MD - Doctor of Medicine',
                        'institution' => 'Cairo University Faculty of Medicine',
                        'year' => '2002',
                        'location' => 'Cairo, Egypt',
                    ],
                    [
                        'degree' => 'Orthopedic Surgery Residency',
                        'institution' => 'Mayo Clinic',
                        'year' => '2007',
                        'location' => 'Rochester, MN, USA',
                    ],
                    [
                        'degree' => 'Fellowship in Sports Medicine',
                        'institution' => 'Hospital for Special Surgery',
                        'year' => '2009',
                        'location' => 'New York, NY, USA',
                    ],
                    [
                        'degree' => 'Board Certification in Orthopedic Surgery',
                        'institution' => 'American Board of Orthopedic Surgery',
                        'year' => '2010',
                        'location' => 'USA',
                    ],
                ],
                'experience' => [
                    [
                        'position' => 'Chief Orthopedic Surgeon',
                        'institution' => 'Medical Excellence Clinic',
                        'start_date' => '2018',
                        'end_date' => 'Present',
                        'description' => 'Leading the orthopedic department and performing complex joint replacement and arthroscopic surgeries.',
                    ],
                    [
                        'position' => 'Senior Orthopedic Surgeon',
                        'institution' => 'Sports Medicine Institute',
                        'start_date' => '2010',
                        'end_date' => '2018',
                        'description' => 'Specialized in treating sports injuries and performing minimally invasive orthopedic procedures.',
                    ],
                ],
                'status' => DoctorProfile::STATUS_APPROVED,
                'reviewed_at' => now(),
                'is_featured' => false,
            ]);

            // Add profile images using UI Avatars
            $this->addProfileImage($profile1, $doctors[0]->name);
            $this->addProfileImage($profile2, $doctors[1]->name);
            $this->addProfileImage($profile3, $doctors[2]->name);

            $this->command->info('Created doctor profiles successfully!');

        } catch (\Exception $e) {
            $this->command->error('Error creating doctor profiles: ' . $e->getMessage());
        }
    }

    /**
     * Add profile image from UI Avatars
     */
    private function addProfileImage($profile, $name)
    {
        try {
            // Generate avatar URL
            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=512&background=0D8ABC&color=fff';

            // Download the image
            $imageContent = Http::get($avatarUrl)->body();

            // Create a temporary file
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.png';
            file_put_contents($tempPath, $imageContent);

            // Add to media library
            $profile->addMedia($tempPath)
                ->usingName($name)
                ->toMediaCollection('profile_image');

            // Clean up temp file
            @unlink($tempPath);

            $this->command->info('Added profile image for: ' . $name);
        } catch (\Exception $e) {
            $this->command->warn('Could not add profile image for ' . $name . ': ' . $e->getMessage());
        }
    }
}
