<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkingHour;
use App\Models\ClinicUser;
use App\Models\DoctorProfile;
use App\Services\Appointment\PeriodGeneratorService;

class WorkingHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periodGenerator = new PeriodGeneratorService();

        // Get the doctors from ClinicUserSeeder
        // Doctor 1: Dr. John Smith (Cardiologist)
        $doctor1ClinicUser = ClinicUser::where('email', 'doctor1@clinic1.com')->first();

        // Doctor 2: Dr. Sarah Johnson (Pediatrician)
        $doctor2ClinicUser = ClinicUser::where('email', 'doctor2@clinic1.com')->first();

        // Doctor 3: Dr. Ahmed Hassan (Orthopedic Surgeon)
        $doctor3ClinicUser = ClinicUser::where('email', 'doctor3@clinic1.com')->first();

        if (!$doctor1ClinicUser || !$doctor2ClinicUser || !$doctor3ClinicUser) {
            $this->command->error('Doctors not found! Please run ClinicUserSeeder first.');
            return;
        }

        // Create working hours for Doctor 1: Dr. John Smith (Cardiologist)
        // Works on Mondays (1) and Wednesdays (3)
        // Morning period: 9:00 AM - 12:00 PM
        // Afternoon period: 2:00 PM - 5:00 PM
        $this->createWorkingHours($doctor1ClinicUser->id, 1, [ // Monday
            ['09:00:00', '12:00:00'],
            ['14:00:00', '17:00:00'],
        ]);

        $this->createWorkingHours($doctor1ClinicUser->id, 3, [ // Wednesday
            ['09:00:00', '12:00:00'],
            ['14:00:00', '17:00:00'],
        ]);

        $this->command->info('Created working hours for Dr. John Smith (Cardiologist) - Mondays & Wednesdays');

        // Create working hours for Doctor 2: Dr. Sarah Johnson (Pediatrician)
        // Works on Tuesdays (2) and Thursdays (4)
        // Morning period: 8:00 AM - 11:00 AM
        // Afternoon period: 1:00 PM - 4:00 PM
        $this->createWorkingHours($doctor2ClinicUser->id, 2, [ // Tuesday
            ['08:00:00', '11:00:00'],
            ['13:00:00', '16:00:00'],
        ]);

        $this->createWorkingHours($doctor2ClinicUser->id, 4, [ // Thursday
            ['08:00:00', '11:00:00'],
            ['13:00:00', '16:00:00'],
        ]);

        $this->command->info('Created working hours for Dr. Sarah Johnson (Pediatrician) - Tuesdays & Thursdays');

        // Create working hours for Doctor 3: Dr. Ahmed Hassan (Orthopedic Surgeon)
        // Works on Sundays (0) and Wednesdays (3)
        // Morning period: 10:00 AM - 1:00 PM
        // Evening period: 3:00 PM - 6:00 PM
        $this->createWorkingHours($doctor3ClinicUser->id, 0, [ // Sunday
            ['10:00:00', '13:00:00'],
            ['15:00:00', '18:00:00'],
        ]);

        $this->createWorkingHours($doctor3ClinicUser->id, 3, [ // Wednesday
            ['10:00:00', '13:00:00'],
            ['15:00:00', '18:00:00'],
        ]);

        $this->command->info('Created working hours for Dr. Ahmed Hassan (Orthopedic Surgeon) - Sundays & Wednesdays');

        // Generate daily periods for the next 30 days for all doctors
        $this->command->info('Generating daily periods for the next 30 days...');

        try {
            // Get doctor profiles
            $doctor1Profile = DoctorProfile::where('clinic_user_id', $doctor1ClinicUser->id)->first();
            $doctor2Profile = DoctorProfile::where('clinic_user_id', $doctor2ClinicUser->id)->first();
            $doctor3Profile = DoctorProfile::where('clinic_user_id', $doctor3ClinicUser->id)->first();

            if ($doctor1Profile) {
                $periods1 = $periodGenerator->generatePeriodsForDoctor($doctor1Profile->id, 30);
                $this->command->info("Generated {$periods1} periods for Dr. John Smith");
            }

            if ($doctor2Profile) {
                $periods2 = $periodGenerator->generatePeriodsForDoctor($doctor2Profile->id, 30);
                $this->command->info("Generated {$periods2} periods for Dr. Sarah Johnson");
            }

            if ($doctor3Profile) {
                $periods3 = $periodGenerator->generatePeriodsForDoctor($doctor3Profile->id, 30);
                $this->command->info("Generated {$periods3} periods for Dr. Ahmed Hassan");
            }

            $this->command->info('Daily periods generated successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error generating daily periods: ' . $e->getMessage());
        }
    }

    /**
     * Create working hours for a specific day
     */
    private function createWorkingHours($clinicUserId, $dayOfWeek, $periods)
    {
        foreach ($periods as $period) {
            WorkingHour::create([
                'clinic_user_id' => $clinicUserId,
                'day_of_week' => $dayOfWeek,
                'start_time' => $period[0],
                'end_time' => $period[1],
                'is_recurring' => true,
            ]);
        }
    }
}

