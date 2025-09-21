<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', Rule::unique($table, 'email')],
            'phone'             => ['required', 'string', 'max:20'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'role'              => ['required', 'exists:roles,name'],
            'status'            => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'password' => 'Password',
            'role' => 'Role',
            'status' => 'Status',
        ];
    }
}
