<?php

namespace App\Http\Requests\Clinic\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'appointment_id' => 'required|exists:appointments,id',
            'clinic_id' => 'required|exists:clinics,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.drug_name' => 'required|string|max:255',
            'items.*.dose' => 'nullable|string|max:255',
            'items.*.frequency' => 'nullable|string|max:255',
            'items.*.duration' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string',
        ];
    }
}