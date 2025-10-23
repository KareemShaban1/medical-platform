<?php

namespace App\Http\Requests\Clinic\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrescriptionRequest extends FormRequest
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
            'appointment_id' => 'sometimes|required|exists:appointments,id',
            'clinic_id' => 'sometimes|required|exists:clinics,id',
            'patient_id' => 'sometimes|required|exists:patients,id',
            'doctor_profile_id' => 'sometimes|required|exists:doctor_profiles,id',
            'notes' => 'sometimes|nullable|string',
            'items' => 'sometimes|required|array|min:1',
            'items.*.drug_name' => 'sometimes|required|string|max:255',
            'items.*.dose' => 'sometimes|nullable|string|max:255',
            'items.*.frequency' => 'sometimes|nullable|string|max:255',
            'items.*.duration' => 'sometimes|nullable|string|max:255',
            'items.*.notes' => 'sometimes|nullable|string',
        ];
    }
}