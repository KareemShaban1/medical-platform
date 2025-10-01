<?php

namespace App\Http\Requests\Clinic\Job;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobApplicationFieldRequest extends FormRequest
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
            'job_id' => 'required|exists:clinic_jobs,id',
            'fields' => 'required|array|min:1',
            'fields.*.field_name' => 'required|string|max:255|regex:/^[a-z0-9_]+$/',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|string|in:text,email,phone,textarea,file,select,checkbox,radio',
            'fields.*.required' => 'nullable|in:0,1',
            'fields.*.options' => 'nullable|array',
            'fields.*.options.*' => 'nullable|string|max:255',
            'fields.*.order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => 'Please select a job.',
            'job_id.exists' => 'The selected job does not exist.',
            'fields.required' => 'At least one field is required.',
            'fields.array' => 'Fields must be an array.',
            'fields.min' => 'At least one field is required.',
            'fields.*.field_name.required' => 'Field name is required.',
            'fields.*.field_name.regex' => 'Field name must contain only lowercase letters, numbers, and underscores.',
            'fields.*.field_label.required' => 'Field label is required.',
            'fields.*.field_type.required' => 'Field type is required.',
            'fields.*.field_type.in' => 'Invalid field type selected.',
            'fields.*.options.array' => 'Options must be an array.',
            'fields.*.options.*.string' => 'Each option must be a string.',
            'fields.*.order.integer' => 'Order must be a number.',
            'fields.*.order.min' => 'Order must be at least 0.',
        ];
    }
}
