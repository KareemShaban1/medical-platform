<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecializedCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('supplier')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_en' => 'required|string|min:2|max:100',
            'name_ar' => 'required|string|min:2|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name_en.required' => 'English name is required.',
            'name_en.string' => 'English name must be a valid string.',
            'name_en.min' => 'English name must be at least 2 characters.',
            'name_en.max' => 'English name cannot exceed 100 characters.',
            'name_ar.required' => 'Arabic name is required.',
            'name_ar.string' => 'Arabic name must be a valid string.',
            'name_ar.min' => 'Arabic name must be at least 2 characters.',
            'name_ar.max' => 'Arabic name cannot exceed 100 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name_en' => 'English name',
            'name_ar' => 'Arabic name',
        ];
    }
}
