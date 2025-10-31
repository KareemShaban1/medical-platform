<?php

namespace App\Http\Requests\Admin\Announcement;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'link_url' => 'nullable|url',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'target_clinics_all' => 'nullable|boolean',
            'target_suppliers_all' => 'nullable|boolean',
            'clinic_ids' => 'sometimes|array',
            'clinic_ids.*' => 'sometimes|integer|exists:clinics,id',
            'supplier_ids' => 'sometimes|array',
            'supplier_ids.*' => 'sometimes|integer|exists:suppliers,id',
            'status' => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'target_clinics_all' => $this->has('target_clinics_all'),
            'target_suppliers_all' => $this->has('target_suppliers_all'),
            'status' => $this->has('status'),
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function($v){
            $hasClinics = $this->boolean('target_clinics_all') || !empty($this->input('clinic_ids', []));
            $hasSuppliers = $this->boolean('target_suppliers_all') || !empty($this->input('supplier_ids', []));
            if (!$hasClinics && !$hasSuppliers) {
                $v->errors()->add('audience', __('Please select clinics and/or suppliers.'));
            }
        });
    }
}
