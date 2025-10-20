<?php

namespace App\Http\Requests\Clinic\DailyPeriod;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyPeriodRequest extends FormRequest
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
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_open' => 'sometimes|boolean',
            'capacity' => 'sometimes|integer|min:1|max:100',
            'auto_queue' => 'sometimes|boolean',
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
            'start_time.required' => __('Start time is required'),
            'start_time.date_format' => __('Invalid start time format'),
            'end_time.required' => __('End time is required'),
            'end_time.date_format' => __('Invalid end time format'),
            'end_time.after' => __('End time must be after start time'),
            'capacity.min' => __('Capacity must be at least 1'),
            'capacity.max' => __('Capacity cannot exceed 100'),
        ];
    }
}

