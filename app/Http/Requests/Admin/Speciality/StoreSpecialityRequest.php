<?php

namespace App\Http\Requests\Admin\Speciality;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecialityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name_en' => 'required|string|max:255|unique:specialties,name_en',
            'name_ar' => 'required|string|max:255|unique:specialties,name_ar',
        ];
    }
}

