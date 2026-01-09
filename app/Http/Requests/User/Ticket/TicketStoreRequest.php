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
        // Check if any of the supported guards is authenticated
        return auth('patient')->check()
            || auth('clinic')->check()
            || auth('supplier')->check()
            || auth('affiliate')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|exists:ticket_types,id',
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
            'type.required' => __('Please select a ticket type.'),
            'type.exists' => __('Invalid ticket type selected.'),
            'details.required' => __('Please provide ticket details.'),
            'details.min' => __('Ticket details must be at least 10 characters.'),
            'details.max' => __('Ticket details cannot exceed 5000 characters.'),
            'attachments.max' => __('You can upload maximum 5 files.'),
            'attachments.*.mimes' => __('Only jpeg, png, jpg, gif, pdf, doc, docx files are allowed.'),
            'attachments.*.max' => __('Each file must be less than 2MB.'),
        ];
    }
}
