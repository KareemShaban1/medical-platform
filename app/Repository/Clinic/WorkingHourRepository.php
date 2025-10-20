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

            // Get existing working hours for this user
            $existingSlots = WorkingHour::where('clinic_user_id', $user->id)
                ->where('is_recurring', $isRecurring)
                ->get()
                ->keyBy('id');

            $incomingIds = collect($slots)->pluck('id')->filter()->toArray();
            $existingIds = $existingSlots->pluck('id')->toArray();

            // Delete slots that are not in the incoming payload
            $idsToDelete = array_diff($existingIds, $incomingIds);
            if (!empty($idsToDelete)) {
                WorkingHour::whereIn('id', $idsToDelete)->delete();
            }

            // Process incoming slots
            foreach ($slots as $slot) {
                // Validate required fields
                if (!isset($slot['day_of_week'], $slot['start_time'], $slot['end_time'])) {
                    continue;
                }

                $day = (int)$slot['day_of_week'];

                // Validate time range
                if ($slot['end_time'] <= $slot['start_time']) {
                    continue;
                }

                $slotData = [
                    'clinic_user_id' => $user->id,
                    'day_of_week' => $day,
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_recurring' => $isRecurring,
                ];

                // Update existing slot or create new one
                if (isset($slot['id']) && $existingSlots->has($slot['id'])) {
                    // Update existing slot
                    $existingSlot = $existingSlots->get($slot['id']);
                    $existingSlot->update($slotData);
                } else {
                    // Create new slot
                    WorkingHour::create($slotData);
                }
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
