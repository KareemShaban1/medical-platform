<?php

namespace App\Http\Requests\Clinic;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('clinic')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:supplier_specialized_categories,id',
            'description' => 'required|string|min:10|max:2000',
            'quantity' => 'required|integer|min:1',
            'preferred_specs' => 'nullable|string|max:1000',
            'timeline' => 'nullable|date|after:today',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:5120', // 5MB max per file
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
            'category_ids.required' => 'Please select at least one category.',
            'category_ids.min' => 'Please select at least one category.',
            'category_ids.*.exists' => 'One or more selected categories are invalid.',
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 10 characters.',
            'description.max' => 'Description cannot exceed 2000 characters.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'timeline.date' => 'Timeline must be a valid date.',
            'timeline.after' => 'Timeline must be a future date.',
            'attachments.max' => 'You can upload maximum 5 files.',
            'attachments.*.file' => 'Each attachment must be a valid file.',
            'attachments.*.mimes' => 'Attachments must be jpeg, png, jpg, gif, pdf, doc, or docx files.',
            'attachments.*.max' => 'Each attachment cannot exceed 5MB.',
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
            'category_ids' => 'categories',
            'preferred_specs' => 'preferred specifications',
        ];
    }
}
