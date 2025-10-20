<?php

namespace App\Http\Requests\Clinic\AvailabilityOverride;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilityOverrideRequest extends FormRequest
{
    public function authorize()
    {
        return auth('clinic')->check();
    }

    public function rules()
    {
        return [
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'type' => 'required|in:blocked,opened',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'doctor_profile_id.required' => __('Please select a doctor'),
            'doctor_profile_id.exists' => __('Selected doctor does not exist'),
            'date.required' => __('Date is required'),
            'date.date' => __('Invalid date format'),
            'date.after_or_equal' => __('Date must be today or in the future'),
            'start_time.date_format' => __('Invalid start time format'),
            'end_time.date_format' => __('Invalid end time format'),
            'end_time.after' => __('End time must be after start time'),
            'type.required' => __('Type is required'),
            'type.in' => __('Invalid type selected'),
        ];
    }
}

