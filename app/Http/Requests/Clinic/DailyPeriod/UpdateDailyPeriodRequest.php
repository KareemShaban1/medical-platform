<?php

namespace App\Http\Requests\Clinic\DailyPeriod;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyPeriodRequest extends FormRequest
{
    public function authorize()
    {
        return auth('clinic')->check();
    }

    public function rules()
    {
        return [
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'is_open' => 'sometimes|boolean',
            'capacity' => 'sometimes|integer|min:1|max:100',
            'auto_queue' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'date.date' => __('Invalid date format'),
            'start_time.date_format' => __('Invalid start time format'),
            'end_time.date_format' => __('Invalid end time format'),
            'end_time.after' => __('End time must be after start time'),
            'capacity.min' => __('Capacity must be at least 1'),
            'capacity.max' => __('Capacity cannot exceed 100'),
        ];
    }
}

