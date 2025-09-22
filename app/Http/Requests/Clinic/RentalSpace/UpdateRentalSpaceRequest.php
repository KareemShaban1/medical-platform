<?php

namespace App\Http\Requests\Clinic\RentalSpace;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalSpaceRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|boolean',
            'main_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'sometimes|array',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Availability (single)
            'availability.type' => 'nullable|string|in:daily,weekly,monthly',
            'availability.from_time' => 'nullable',
            'availability.to_time' => 'nullable',
            'availability.from_date' => 'nullable|date',
            'availability.to_date' => 'nullable|date',

            // Pricing (single)
            'pricing.price' => 'nullable|numeric|min:0',
            'pricing.notes' => 'nullable|string|max:255',
        ];
    }
}
