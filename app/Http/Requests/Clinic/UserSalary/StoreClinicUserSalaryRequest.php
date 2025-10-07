<?php

namespace App\Http\Requests\Clinic\UserSalary;

use Illuminate\Foundation\Http\FormRequest;

class StoreClinicUserSalaryRequest extends FormRequest
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
            'amount' => 'required|numeric',
            'salary_frequency' => 'required|in:daily,weekly,monthly',
            'amount_per_salary_frequency' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'paid' => 'required|boolean',
            'notes' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}