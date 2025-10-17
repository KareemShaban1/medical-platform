<?php

namespace App\Http\Requests\Admin\Speciality;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecialityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('speciality');
        return [
            'name_en' => 'required|string|max:255|unique:specialties,name_en,' . $id,
            'name_ar' => 'required|string|max:255|unique:specialties,name_ar,' . $id,
        ];
    }
}

