<?php

namespace App\Http\Requests\Clinic\MedicalRecord;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('clinic')->check();
    }

    public function rules(): array
    {
        return [
            'visit_type' => ['required', 'in:0,1,2'],
            'chief_complaint' => ['nullable', 'string', 'max:500'],
            'diagnosis' => ['nullable', 'string', 'max:1000'],
            'treatment' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_shared_with_patient' => ['nullable', 'boolean'],
        ];
    }
}
