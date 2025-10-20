<?php

namespace App\Services\Appointment;

use App\Models\DoctorProfile;
use App\Models\WorkingHour;
use App\Models\AvailabilityOverride;
use App\Models\DailyPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PeriodGeneratorService
{
    /**
     * Generate daily periods for all approved doctors for the next X days
     */
    public function generatePeriodsForAllDoctors(int $daysAhead = 30): array
    {
        $doctors = DoctorProfile::where('status', DoctorProfile::STATUS_APPROVED)->get();
        $results = ['success' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($doctors as $doctor) {
            try {
                $this->generatePeriodsForDoctor($doctor->id, $daysAhead);
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors']++;
                \Log::error("Failed to generate periods for doctor {$doctor->id}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Generate daily periods for a specific doctor
     */
    public function generatePeriodsForDoctor(int $doctorProfileId, int $daysAhead = 30): int
    {
        $doctor = DoctorProfile::findOrFail($doctorProfileId);

        // Get clinic_user_id from doctor profile
        $clinicUserId = $doctor->clinic_user_id;

        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($daysAhead);

        $periodsCreated = 0;

        // Get all working hours for this doctor's clinic user
        $workingHours = WorkingHour::where('clinic_user_id', $clinicUserId)
            ->where('is_recurring', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        // Get availability overrides for the date range
        $overrides = AvailabilityOverride::where('doctor_profile_id', $doctorProfileId)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy(function ($item) {
                return $item->date->toDateString();
            });

        // Loop through each day
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek; // 0=Sunday, 6=Saturday
            $dateString = $date->toDateString();

            // Check if there are overrides for this date
            $dayOverrides = $overrides->get($dateString, collect());

            // Check if entire day is blocked
            $dayBlocked = $dayOverrides->contains(function ($override) {
                return $override->type === 'blocked' &&
                       is_null($override->start_time) &&
                       is_null($override->end_time);
            });

            if ($dayBlocked) {
                continue; // Skip this day entirely
            }

            // Get working hours for this day of week
            $dayWorkingHours = $workingHours->get($dayOfWeek, collect());

            // Process each working hour slot
            foreach ($dayWorkingHours as $workingHour) {
                $startTime = $workingHour->start_time;
                $endTime = $workingHour->end_time;

                // Check if this specific time slot is blocked by an override
                $slotBlocked = $dayOverrides->contains(function ($override) use ($startTime, $endTime) {
                    if ($override->type !== 'blocked') return false;
                    if (is_null($override->start_time) || is_null($override->end_time)) return false;

                    // Check if there's an overlap
                    return !($endTime <= $override->start_time || $startTime >= $override->end_time);
                });

                if ($slotBlocked) {
                    continue; // Skip this slot
                }

                // Check if period already exists
                $exists = DailyPeriod::where('doctor_profile_id', $doctorProfileId)
                    ->where('date', $dateString)
                    ->where('start_time', $startTime)
                    ->where('end_time', $endTime)
                    ->exists();

                if (!$exists) {
                    DailyPeriod::create([
                        'doctor_profile_id' => $doctorProfileId,
                        'date' => $dateString,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_open' => true,
                        'capacity' => 10, // Default capacity
                        'booked_count' => 0,
                        'auto_queue' => false,
                    ]);
                    $periodsCreated++;
                }
            }

            // Process "opened" overrides (manually added availability)
            $openedOverrides = $dayOverrides->filter(function ($override) {
                return $override->type === 'opened' &&
                       !is_null($override->start_time) &&
                       !is_null($override->end_time);
            });

            foreach ($openedOverrides as $override) {
                $exists = DailyPeriod::where('doctor_profile_id', $doctorProfileId)
                    ->where('date', $dateString)
                    ->where('start_time', $override->start_time)
                    ->where('end_time', $override->end_time)
                    ->exists();

                if (!$exists) {
                    DailyPeriod::create([
                        'doctor_profile_id' => $doctorProfileId,
                        'date' => $dateString,
                        'start_time' => $override->start_time,
                        'end_time' => $override->end_time,
                        'is_open' => true,
                        'capacity' => 10,
                        'booked_count' => 0,
                        'auto_queue' => false,
                    ]);
                    $periodsCreated++;
                }
            }
        }

        return $periodsCreated;
    }

    /**
     * Regenerate periods for a specific doctor (delete and recreate)
     */
    public function regeneratePeriodsForDoctor(int $doctorProfileId, int $daysAhead = 30): int
    {
        // Delete future periods that haven't been booked
        DailyPeriod::where('doctor_profile_id', $doctorProfileId)
            ->where('date', '>=', Carbon::today()->toDateString())
            ->where('booked_count', 0)
            ->delete();

        return $this->generatePeriodsForDoctor($doctorProfileId, $daysAhead);
    }
}

