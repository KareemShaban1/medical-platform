<?php

namespace App\Http\Requests\Clinic\SalaryContract;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryContractRequest extends FormRequest
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
            'clinic_user_id' => 'required|exists:clinic_users,id',
            'salary_type' => 'required|in:fixed,hours,percentage,bonus,deduction',
            'base_amount' => 'nullable|numeric',
            'percentage_rate' => 'nullable|numeric',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'notes' => 'nullable|string',
        ];
    }
}
