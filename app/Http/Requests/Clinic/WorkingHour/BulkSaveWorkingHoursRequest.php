<?php

namespace App\Http\Requests\Clinic\WorkingHour;

use Illuminate\Foundation\Http\FormRequest;

class BulkSaveWorkingHoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('clinic')->check();
    }

    public function rules(): array
    {
        return [
            'clinic_user_id' => 'required|exists:clinic_users,id',
            'is_recurring' => 'nullable|boolean',

            'slots' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    // ✅ Check for duplicates *inside the request only*
                    $uniqueSlots = collect($value)->unique(function ($slot) {
                        return $slot['day_of_week'] . '-' . $slot['start_time'] . '-' . $slot['end_time'];
                    });

                    if ($uniqueSlots->count() !== count($value)) {
                        $fail('You have duplicate time slots in your request payload.');
                    }
                }
            ],

            'slots.*.id' => 'nullable|integer|exists:working_hours,id',
            'slots.*.day_of_week' => 'required|integer|min:0|max:6',
            'slots.*.start_time' => 'required',
            'slots.*.end_time' => 'required|after:slots.*.start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'slots.*.day_of_week.required' => 'Day of week is required.',
            'slots.*.start_time.required' => 'Start time is required.',
            'slots.*.end_time.required' => 'End time is required.',
            'slots.*.end_time.after' => 'End time must be after the start time.',
        ];
    }
}
