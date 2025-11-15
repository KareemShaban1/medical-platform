<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\ClinicInventoryMovementRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\ClinicInventoryMovement;
use App\Models\ClinicInventory;

class ClinicInventoryMovementRepository implements ClinicInventoryMovementRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data($id)
    {
        $clinicInventoryMovements = ClinicInventoryMovement::where('clinic_inventory_id', $id)->get();

        return datatables()->of($clinicInventoryMovements)
            ->editColumn('clinic_inventory', fn($item) => $item->clinicInventory->item_name)
            ->addColumn('action', fn($item) => $this->clinicInventoryMovementActions($item))
            ->rawColumns(['action', 'clinic_inventory'])
            ->make(true);
    }

    public function show($id){
        return ClinicInventoryMovement::findOrFail($id);
    }


    public function store($request)
    {
        return $this->saveClinicInventoryMovement(new ClinicInventoryMovement(), $request, 'created');
    }

    public function update($request, $id)
    {
        $clinicInventoryMovement = ClinicInventoryMovement::findOrFail($id);
        return $this->saveClinicInventoryMovement($clinicInventoryMovement, $request, 'updated');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $movement = ClinicInventoryMovement::findOrFail($id);
            $inventory = $movement->clinicInventory()->lockForUpdate()->first();

            if ($movement->type === 'in') {
                $newQty = (int) $inventory->quantity - (int) $movement->quantity;
                if ($newQty < 0) { $newQty = 0; }
                $inventory->quantity = $newQty;
            } else { // out
                $inventory->quantity = (int) $inventory->quantity + (int) $movement->quantity;
            }

            $inventory->save();
            $movement->delete();
            DB::commit();

            return $this->jsonResponse('success', __('Clinic inventory movement deleted and stock updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $clinicInventoryMovements = ClinicInventoryMovement::onlyTrashed()->get();

        return datatables()->of($clinicInventoryMovements)
            ->editColumn('clinic_inventory', fn($item) => $item->clinicInventory->item_name)
            ->addColumn('trash_action', fn($item) => $this->clinicInventoryMovementTrashActions($item))
            ->rawColumns(['trash_action', 'clinic_inventory'])
            ->make(true);
    }

    public function restore($id)
    {
        $clinicInventoryMovement = ClinicInventoryMovement::onlyTrashed()->findOrFail($id);
        $clinicInventoryMovement->restore();

        return $this->jsonResponse('success', __('Clinic inventory restored successfully'));
    }

    public function forceDelete($id)
    {
        $clinicInventoryMovement = ClinicInventoryMovement::onlyTrashed()->findOrFail($id);
        $clinicInventoryMovement->forceDelete();


        return $this->jsonResponse('success', __('Clinic inventory deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveClinicInventoryMovement($clinicInventoryMovement, $request, string $action)
    {
        try {
            DB::beginTransaction();

            // Lock inventory row for safe concurrent updates
            $inventory = $clinicInventoryMovement->exists
                ? $clinicInventoryMovement->clinicInventory()->lockForUpdate()->first()
                : ClinicInventory::lockForUpdate()->findOrFail($request->clinic_inventory_id);

            // Previous state if updating
            $previousQty = 0;
            $previousType = null;
            if ($clinicInventoryMovement->exists) {
                $previousQty = (int) $clinicInventoryMovement->quantity;
                $previousType = $clinicInventoryMovement->type;
            }

            // Do not allow changing type on update
            $newType = $previousType ?? $request->type;
            $newQty = (int) $request->quantity;

            // Reverse previous impact
            if ($previousType === 'in') {
                $inventory->quantity = (int) $inventory->quantity - $previousQty;
            } elseif ($previousType === 'out') {
                $inventory->quantity = (int) $inventory->quantity + $previousQty;
            }

            // Validate and apply new impact
            if ($newType === 'out' && $newQty > (int) $inventory->quantity) {
                throw new \Exception(__('The out quantity exceeds current stock.'));
            }

            if ($newType === 'in') {
                $inventory->quantity = (int) $inventory->quantity + $newQty;
            } else { // out
                $inventory->quantity = (int) $inventory->quantity - $newQty;
            }

            // Save movement and inventory
            $payload = $request->validated();
            $payload['type'] = $newType;
            $clinicInventoryMovement->fill($payload)->save();
            $inventory->save();

            DB::commit();

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Clinic inventory ' . $action . ' successfully'));
            }

            return redirect()->route('clinic.clinic-inventory-movements.index', $clinicInventoryMovement->clinicInventory->id)
                ->with('success', __('Clinic inventory ' . $action . ' successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function clinicInventoryMovementActions($item): string
    {
        $editUrl = route('clinic.clinic-inventory-movements.edit', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$editUrl}" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></a>
           <button onclick="deleteClinicInventoryMovement({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function clinicInventoryMovementTrashActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
           <button onclick="restore({$item->id})" class="btn btn-sm btn-info" title="Restore"><i class="fa fa-undo"></i></button>
           <button onclick="forceDelete({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }


    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
