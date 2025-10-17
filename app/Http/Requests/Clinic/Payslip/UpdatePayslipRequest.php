<?php

namespace App\Http\Requests\Clinic\Payslip;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayslipRequest extends FormRequest
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
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'status' => 'required|string|in:pending,paid,unpaid',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|string',
            'items.*.notes' => 'required|string',
            'items.*.amount' => 'required|numeric',
        ];
    }
}
