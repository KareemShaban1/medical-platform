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
            'slots' => 'nullable|array',
            'slots.*.day_of_week' => 'required|integer|min:0|max:6',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i',
            'is_recurring' => 'nullable|boolean',
        ];
    }
}

