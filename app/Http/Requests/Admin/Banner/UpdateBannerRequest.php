<?php

namespace App\Http\Requests\Admin\Banner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'open_in_new_tab' => 'nullable|boolean',
            'position' => 'required|string|max:100',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'required|boolean',
            'priority' => 'nullable|integer|min:0|max:999',
            'target_pages' => 'nullable|array',
            'target_pages.*' => 'nullable|string',
            'target_categories' => 'nullable|array',
            'target_categories.*' => 'nullable|integer|exists:categories,id',
            'target_products' => 'nullable|array',
            'target_products.*' => 'nullable|integer',
            'show_on_all_pages' => 'nullable|boolean',
            'text_position' => 'nullable|in:top-left,top-center,top-right,center-left,center,center-right,bottom-left,bottom-center,bottom-right,custom',
            'text_position_custom' => 'nullable|array',
            'text_position_custom.top' => 'nullable|string',
            'text_position_custom.left' => 'nullable|string',
            'text_position_custom.right' => 'nullable|string',
            'text_position_custom.bottom' => 'nullable|string',
            'button_position' => 'nullable|in:below-text,above-text,left-of-text,right-of-text,custom',
            'button_position_custom' => 'nullable|array',
            'button_position_custom.top' => 'nullable|string',
            'button_position_custom.left' => 'nullable|string',
            'button_position_custom.right' => 'nullable|string',
            'button_position_custom.bottom' => 'nullable|string',
            'text_color' => 'nullable|string|max:7',
            'text_background_color' => 'nullable|string|max:7',
            'text_background_opacity' => 'nullable|integer|min:0|max:100',
            'text_alignment' => 'nullable|in:left,center,right',
            'button_color' => 'nullable|string|max:7',
            'button_text_color' => 'nullable|string|max:7',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'open_in_new_tab' => $this->has('open_in_new_tab'),
            'show_on_all_pages' => $this->has('show_on_all_pages'),
            'status' => $this->has('status'),
        ]);
    }
}
