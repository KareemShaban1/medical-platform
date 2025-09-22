<?php

namespace App\Http\Requests\Clinic\DoctorProfile;

use Illuminate\Foundation\Http\FormRequest;

class DoctorProfileStoreRequest extends FormRequest
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
            'bio' => 'nullable|string|max:2000',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'twitter_link' => 'nullable|url|max:255',
            'linkedin_link' => 'nullable|url|max:255',
            'facebook_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'research_links' => 'nullable|array',
            'research_links.*' => 'url|max:255',
            'years_experience' => 'nullable|integer|min:0|max:50',
            'specialties' => 'nullable|array',
            'specialties.*' => 'string|max:100',
            'services_offered' => 'nullable|array',
            'services_offered.*' => 'string|max:100',
            'education' => 'nullable|array',
            'education.*.degree' => 'nullable|string|max:255',
            'education.*.institution' => 'nullable|string|max:255',
            'education.*.year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'experience' => 'nullable|array',
            'experience.*.position' => 'nullable|string|max:255',
            'experience.*.company' => 'nullable|string|max:255',
            'experience.*.start_year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'experience.*.end_year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'experience.*.description' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The doctor name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'profile_photo.mimes' => 'Profile photo must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            'profile_photo.max' => 'Profile photo may not be greater than 2MB.',
            'research_links.*.url' => 'Each research link must be a valid URL.',
            'twitter_link.url' => 'Twitter link must be a valid URL.',
            'linkedin_link.url' => 'LinkedIn link must be a valid URL.',
            'facebook_link.url' => 'Facebook link must be a valid URL.',
            'instagram_link.url' => 'Instagram link must be a valid URL.',
            'years_experience.integer' => 'Years of experience must be a valid number.',
            'years_experience.min' => 'Years of experience cannot be negative.',
            'years_experience.max' => 'Years of experience cannot exceed 50 years.',
        ];
    }
}
