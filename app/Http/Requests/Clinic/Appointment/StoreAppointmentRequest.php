<?php

namespace App\Http\Requests\Clinic\Appointment;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return auth('clinic')->check();
    }

    public function rules()
    {
        return [
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'patient_id' => 'required|exists:patients,id',
            'period_id' => 'required|exists:daily_periods,id',
            'patient_notes' => 'nullable|string|max:1000',
            'doctor_notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:' . implode(',', array_keys(Appointment::getStatuses())),
            'visit_type' => 'nullable|integer|in:0,1,2',
            'cost_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,paid',
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
            'visit_type.in' => __('Invalid visit type selected'),
            'cost_amount.numeric' => __('Cost amount must be a number'),
            'payment_status.in' => __('Invalid payment status selected'),
        ];
    }
}

