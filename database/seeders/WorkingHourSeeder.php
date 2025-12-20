<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkingHour;
use App\Models\DoctorProfile;
use App\Models\ClinicUser;
use App\Services\Appointment\PeriodGeneratorService;
use Carbon\Carbon;

class WorkingHourSeeder extends Seeder
{
    // Define working schedules
    private $schedules = [
        'morning_shift' => [
            'days' => [0, 2, 4], // Sunday, Tuesday, Thursday
            'periods' => [['08:00:00', '12:00:00']],
        ],
        'afternoon_shift' => [
            'days' => [1, 3], // Monday, Wednesday
            'periods' => [['14:00:00', '18:00:00']],
        ],
        'full_day' => [
            'days' => [0, 2], // Sunday, Tuesday
            'periods' => [['09:00:00', '13:00:00'], ['15:00:00', '19:00:00']],
        ],
        'evening_shift' => [
            'days' => [1, 3, 5], // Monday, Wednesday, Friday
            'periods' => [['16:00:00', '20:00:00']],
        ],
    ];

    public function run(): void
    {
        $this->command->info('Creating working hours for all doctors...');

        $periodGenerator = new PeriodGeneratorService();

        $doctors = DoctorProfile::with('clinicUser')->where('status', DoctorProfile::STATUS_APPROVED)->get();

        if ($doctors->isEmpty()) {
            $this->command->error('No doctors found! Please run ClinicUserSeeder first.');
            return;
        }

        $totalPeriods = 0;

        foreach ($doctors as $index => $doctor) {
            // Assign a random schedule to each doctor
            $scheduleKey = array_rand($this->schedules);
            $schedule = $this->schedules[$scheduleKey];

            $this->command->info("\nSetting up {$doctor->name} ({$scheduleKey})...");

            // Create working hours
            foreach ($schedule['days'] as $day) {
                foreach ($schedule['periods'] as $period) {
                    WorkingHour::create([
                        'clinic_user_id' => $doctor->clinic_user_id,
                        'day_of_week' => $day,
                        'start_time' => $period[0],
                        'end_time' => $period[1],
                        'is_recurring' => true,
                    ]);
                }
            }

            // Generate daily periods for the next 60 days
            try {
                $periodsCount = $periodGenerator->generatePeriodsForDoctorInRange(
                    $doctor->id,
                    Carbon::today()->subDays(30),
                    Carbon::today()->addDays(60)
                );
                $totalPeriods += $periodsCount;
                $this->command->info("  ✓ Generated {$periodsCount} periods for {$doctor->name}");
            } catch (\Exception $e) {
                $this->command->error("  Error generating periods: " . $e->getMessage());
            }
        }

        $this->command->info("\n✓ Successfully created working hours and {$totalPeriods} daily periods for " . $doctors->count() . " doctors");
    }
}
