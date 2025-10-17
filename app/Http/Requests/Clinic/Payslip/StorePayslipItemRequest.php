<?php

namespace App\Http\Requests\Clinic\Payslip;

use Illuminate\Foundation\Http\FormRequest;

class StorePayslipItemRequest extends FormRequest
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
            'payslip_id' => 'required|exists:payslips,id',
            'type' => 'required|in:fixed,hours,percentage,bonus,deduction',
            'notes' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ];
    }
}
