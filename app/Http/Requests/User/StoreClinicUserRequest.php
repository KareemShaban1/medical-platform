<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClinicUserRequest extends FormRequest
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
        $guard = $this->route()->getName();
        $guard = strpos($guard, 'clinic.') === 0 ? 'clinic' : 'supplier';
        $table = $guard === 'clinic' ? 'clinic_users' : 'supplier_users';

        return [
            'name' => ['required', 'string', 'max:255'],
            'position_title' => [$guard === 'clinic' ? 'required' : 'nullable', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns,spoof', 'max:255', Rule::unique($table, 'email')],
            'phone' => ['required', 'string', 'max:20',  'phone:EG'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'position_title' => 'Position Title',
            'email' => 'Email',
            'phone' => 'Phone',
            'password' => 'Password',
            'role' => 'Role',
            'status' => 'Status',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => __('This email is already in use.'),
        ];
    }
}
