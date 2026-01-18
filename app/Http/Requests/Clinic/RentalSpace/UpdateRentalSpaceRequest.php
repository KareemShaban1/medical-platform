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
            'location' => 'required|string|max:500',
            'description' => 'required|string',
            'status' => 'required|boolean',

            // New fields
            'listing_type' => 'required|in:rent,sale',
            'sale_price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'area_sqm' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:50',

            // Images (optional on update)
            'main_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // Availability
            'availability.type' => 'nullable|string|in:daily,weekly,monthly',
            'availability.from_time' => 'nullable|date_format:H:i',
            'availability.to_time' => 'nullable|date_format:H:i',
            'availability.from_date' => 'nullable|date',
            'availability.to_date' => 'nullable|date|after_or_equal:availability.from_date',

            // Pricing
            'pricing.pricing_type' => 'nullable|in:hourly,daily,weekly,monthly',
            'pricing.price' => 'nullable|numeric|min:0',
            'pricing.notes' => 'nullable|string|max:500',

            // Schedules (recurring availability)
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required_with:schedules|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i',
            'schedules.*.is_available' => 'nullable|boolean',
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

            'listing_type.required' => __('Please select whether this is for rent or sale.'),
            'listing_type.in' => __('Listing type must be rent or sale.'),

            'main_image.image' => __('The main image must be a valid image file.'),
            'main_image.mimes' => __('The main image must be a jpeg, png, jpg, gif, or webp file.'),
            'main_image.max' => __('The main image may not be greater than 2MB.'),

            'images.*.image' => __('Each gallery image must be a valid image file.'),
            'images.*.mimes' => __('Each gallery image must be a jpeg, png, jpg, gif, or webp file.'),
            'images.*.max' => __('Each gallery image may not be greater than 2MB.'),

            'availability.type.in' => __('Availability type must be daily, weekly, or monthly.'),
            'availability.to_date.after_or_equal' => __('End date must be after or equal to start date.'),

            'pricing.pricing_type.in' => __('Pricing type must be hourly, daily, weekly, or monthly.'),
            'pricing.price.numeric' => __('Price must be a number.'),
            'pricing.price.min' => __('Price must be at least 0.'),
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
            'listing_type' => __('listing type'),
            'sale_price' => __('sale price'),
            'capacity' => __('capacity'),
            'area_sqm' => __('area'),
            'amenities' => __('amenities'),
            'availability.type' => __('availability type'),
            'availability.from_time' => __('availability start time'),
            'availability.to_time' => __('availability end time'),
            'availability.from_date' => __('availability start date'),
            'availability.to_date' => __('availability end date'),
            'pricing.pricing_type' => __('pricing type'),
            'pricing.price' => __('price'),
            'pricing.notes' => __('pricing notes'),
            'schedules.*.start_time' => __('start time'),
            'schedules.*.end_time' => __('end time'),
        ];
    }
}
