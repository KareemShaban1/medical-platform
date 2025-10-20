<?php

namespace App\Http\Requests\Clinic\AvailabilityOverride;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvailabilityOverrideRequest extends FormRequest
{
    public function authorize()
    {
        return auth('clinic')->check();
    }

    public function rules()
    {
        return [
            'date' => 'sometimes|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'type' => 'sometimes|in:blocked,opened',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'date.date' => __('Invalid date format'),
            'date.after_or_equal' => __('Date must be today or in the future'),
            'start_time.date_format' => __('Invalid start time format'),
            'end_time.date_format' => __('Invalid end time format'),
            'end_time.after' => __('End time must be after start time'),
            'type.in' => __('Invalid type selected'),
        ];
    }
}

