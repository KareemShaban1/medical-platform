<?php

namespace App\Http\Requests\Clinic\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ClinicInventory;

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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $quantity = (int) $this->input('quantity');
            $inventoryId = $this->input('clinic_inventory_id');

            if ($type === 'out' && $inventoryId && $quantity > 0) {
                $inventory = ClinicInventory::find($inventoryId);
                if ($inventory && $quantity > (int) $inventory->quantity) {
                    $validator->errors()->add('quantity', __('The out quantity exceeds current stock (available: :available).', ['available' => $inventory->quantity]));
                }
            }
        });
    }
}
