<?php

namespace App\Http\Requests\User\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketReplyRequest extends FormRequest
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
            'message' => 'required|string|min:5|max:2000',
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
            'message.required' => 'Please enter your reply message.',
            'message.min' => 'Reply message must be at least 5 characters.',
            'message.max' => 'Reply message cannot exceed 2000 characters.',
        ];
    }
}
