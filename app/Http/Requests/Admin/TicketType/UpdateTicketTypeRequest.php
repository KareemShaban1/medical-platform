<?php

namespace App\Http\Requests\Admin\TicketType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('ticket_types', 'slug')->ignore($this->route('ticket_type')),
            ],
            'description' => 'nullable|string|max:1000',
            'badge_color' => 'required|string|max:20',
            'is_active' => 'boolean',
            'user_types' => 'required|array|min:1',
            'user_types.*' => 'string|in:user,clinic_user,supplier_user,affiliate_user',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('Ticket type name is required'),
            'user_types.required' => __('At least one user type must be selected'),
            'user_types.min' => __('At least one user type must be selected'),
        ];
    }
}
