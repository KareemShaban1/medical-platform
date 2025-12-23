<?php

namespace App\Http\Requests\Clinic\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ClinicInventory;
use App\Models\ClinicInventoryMovement;

class UpdateClinicInventoryMovementRequest extends FormRequest
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
            $movementId = $this->route('id');

            if ($type === 'out' && $inventoryId && $quantity > 0) {
                $inventory = ClinicInventory::find($inventoryId);
                $available = $inventory ? (int) $inventory->quantity : 0;

                if ($movementId && $inventory) {
                    $movement = ClinicInventoryMovement::find($movementId);
                    if ($movement && (int) $movement->clinic_inventory_id === (int) $inventoryId) {
                        if ($movement->type === 'out') {
                            $available += (int) $movement->quantity;
                        } elseif ($movement->type === 'in') {
                            $available -= (int) $movement->quantity;
                        }
                    }
                }

                if ($inventory && $quantity > $available) {
                    $validator->errors()->add('quantity', __('The out quantity exceeds current stock (available: :available).', ['available' => max(0, $available)]));
                }
            }
        });
    }
}
