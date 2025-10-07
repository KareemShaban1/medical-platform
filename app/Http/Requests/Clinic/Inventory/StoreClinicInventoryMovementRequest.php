<?php

namespace App\Http\Requests\Clinic\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreClinicInventoryMovementRequest extends FormRequest
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
            'clinic_inventory_id' => 'required|exists:clinic_inventories,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            'movement_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}