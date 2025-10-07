<?php

namespace App\Http\Requests\User\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('patient')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:refund,complaint',
            'details' => 'required|string|min:10|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
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
            'type.required' => 'Please select a ticket type.',
            'type.in' => 'Invalid ticket type selected.',
            'details.required' => 'Please provide ticket details.',
            'details.min' => 'Ticket details must be at least 10 characters.',
            'details.max' => 'Ticket details cannot exceed 5000 characters.',
            'attachments.max' => 'You can upload maximum 5 files.',
            'attachments.*.mimes' => 'Only jpeg, png, jpg, gif, pdf, doc, docx files are allowed.',
            'attachments.*.max' => 'Each file must be less than 2MB.',
        ];
    }
}
