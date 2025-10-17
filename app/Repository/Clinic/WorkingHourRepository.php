<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\WorkingHourRepositoryInterface;
use App\Models\ClinicUser;
use App\Models\WorkingHour;
use Illuminate\Support\Facades\DB;

class WorkingHourRepository implements WorkingHourRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function forUser($clinicUserId)
    {
        $user = ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)
            ->findOrFail($clinicUserId);

        $hours = WorkingHour::where('clinic_user_id', $user->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return $hours;
    }

    public function bulkSave($clinicUserId, array $slots, bool $isRecurring = true)
    {
        return DB::transaction(function () use ($clinicUserId, $slots, $isRecurring) {
            $user = ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)
                ->findOrFail($clinicUserId);

            WorkingHour::where('clinic_user_id', $user->id)->where('is_recurring', $isRecurring)->delete();

            $payload = [];
            $seenDays = [];
            foreach ($slots as $slot) {
                // ['day_of_week'=>0..6,'start_time'=>'HH:MM','end_time'=>'HH:MM']
                if (!isset($slot['day_of_week'], $slot['start_time'], $slot['end_time'])) {
                    continue;
                }
                $day = (int)$slot['day_of_week'];
                if (isset($seenDays[$day])) {
                    continue;
                }
                if ($slot['end_time'] <= $slot['start_time']) {
                    continue;
                }
                $payload[] = [
                    'clinic_user_id' => $user->id,
                    'day_of_week' => $day,
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_recurring' => $isRecurring,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $seenDays[$day] = true;
            }

            if (!empty($payload)) {
                WorkingHour::insert($payload);
            }

            return $this->forUser($user->id);
        });
    }

    public function destroy($id)
    {
        $slot = WorkingHour::findOrFail($id);
        $this->assertOwnedByClinic($slot->clinic_user_id);
        return $slot->delete();
    }

    private function assertOwnedByClinic($clinicUserId): void
    {
        $belongs = ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)
            ->where('id', $clinicUserId)
            ->exists();
        if (!$belongs) {
            throw new \Exception(__('Unauthorized action'));
        }
    }
}
