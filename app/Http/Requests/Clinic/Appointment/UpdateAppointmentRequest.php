<?php

namespace App\Http\Requests\Clinic\Appointment;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return auth('clinic')->check();
    }

    public function rules()
    {
        return [
            'doctor_profile_id' => 'sometimes|required|exists:doctor_profiles,id',
            'patient_id' => 'sometimes|required|exists:patients,id',
            'period_id' => 'sometimes|required|exists:daily_periods,id',
            'status' => 'sometimes|in:' . implode(',', array_keys(Appointment::getStatuses())),
            'patient_notes' => 'nullable|string|max:1000',
            'doctor_notes' => 'nullable|string|max:1000',
            'cancellation_reason' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'doctor_profile_id.required' => __('Please select a doctor'),
            'doctor_profile_id.exists' => __('Selected doctor does not exist'),
            'patient_id.required' => __('Please select a patient'),
            'patient_id.exists' => __('Selected patient does not exist'),
            'period_id.required' => __('Please select a time slot'),
            'period_id.exists' => __('Selected time slot does not exist'),
            'status.in' => __('Invalid status selected'),
        ];
    }
}

