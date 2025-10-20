<?php

namespace App\Http\Requests\User\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Can be guest or authenticated
    }

    public function rules()
    {
        return [
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'period_id' => 'required|exists:daily_periods,id',
            'patient_notes' => 'nullable|string|max:1000',
            // Patient info if not logged in
            'name' => 'required_without:user_id|string|max:255',
            'email' => 'required_without:user_id|email|max:255',
            'phone' => 'required_without:user_id|string|max:20',
        ];
    }

    public function messages()
    {
        return [
            'doctor_profile_id.required' => __('Please select a doctor'),
            'doctor_profile_id.exists' => __('Selected doctor does not exist'),
            'period_id.required' => __('Please select a time slot'),
            'period_id.exists' => __('Selected time slot does not exist'),
            'name.required_without' => __('Name is required'),
            'email.required_without' => __('Email is required'),
            'phone.required_without' => __('Phone is required'),
        ];
    }
}

