<?php

namespace App\Http\Requests\User\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'confirmation_code' => 'required|string|size:6',
        ];
    }

    public function messages()
    {
        return [
            'confirmation_code.required' => __('Confirmation code is required'),
            'confirmation_code.size' => __('Confirmation code must be 6 characters'),
        ];
    }
}

