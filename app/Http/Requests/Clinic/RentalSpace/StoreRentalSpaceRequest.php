<?php

namespace App\Http\Requests\Clinic\RentalSpace;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalSpaceRequest extends FormRequest
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
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Availability (single)
            'availability.type' => 'nullable|string|in:daily,weekly,monthly',
            'availability.from_time' => 'nullable',
            'availability.to_time' => 'nullable',
            'availability.from_date' => 'nullable|date',
            'availability.to_date' => 'nullable|date',

            // Pricing (single)
            'pricing.price' => 'required|numeric|min:0',
            'pricing.notes' => 'required|string|max:255',
        ];
    }

    /**
     * Custom messages for clearer, user-friendly validation feedback.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Please enter the rental space name.'),
            'location.required' => __('Please provide the location.'),
            'description.required' => __('Please add a short description.'),
            'status.required' => __('Please choose whether the rental space is active.'),

            'main_image.required' => __('Please upload a main image.'),
            'main_image.image' => __('The main image must be a valid image file (jpeg, png, jpg, gif).'),
            'main_image.mimes' => __('The main image must be a jpeg, png, jpg, or gif file.'),
            'main_image.max' => __('The main image may not be greater than 2MB.'),

            'images.required' => __('Please upload at least one gallery image.'),
            'images.array' => __('Gallery images must be sent as an array.'),
            'images.*.required' => __('Each gallery image is required.'),
            'images.*.image' => __('Each gallery image must be a valid image file (jpeg, png, jpg, gif).'),
            'images.*.mimes' => __('Each gallery image must be a jpeg, png, jpg, or gif file.'),
            'images.*.max' => __('Each gallery image may not be greater than 2MB.'),

            'availability.type.in' => __('Availability type must be daily, weekly, or monthly.'),
            'availability.from_date.date' => __('Availability start date must be a valid date.'),
            'availability.to_date.date' => __('Availability end date must be a valid date.'),

            'pricing.price.required' => __('Please enter a price.'),
            'pricing.price.numeric' => __('Price must be a number.'),
            'pricing.price.min' => __('Price must be at least 0.'),
            'pricing.notes.required' => __('Please add a note about the pricing.'),
            'pricing.notes.max' => __('Pricing notes may not be greater than 255 characters.'),
        ];
    }

    /**
     * Human-friendly attribute names for nested fields.
     */
    public function attributes(): array
    {
        return [
            'main_image' => __('main image'),
            'images' => __('gallery images'),
            'images.*' => __('gallery image'),
            'availability.type' => __('availability type'),
            'availability.from_time' => __('availability start time'),
            'availability.to_time' => __('availability end time'),
            'availability.from_date' => __('availability start date'),
            'availability.to_date' => __('availability end date'),
            'pricing.price' => __('price'),
            'pricing.notes' => __('pricing notes'),
        ];
    }
}
