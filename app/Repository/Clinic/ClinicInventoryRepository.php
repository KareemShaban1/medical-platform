<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\ClinicInventoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Traits\HandlesMediaUploads;
use App\Models\ClinicInventory;

class ClinicInventoryRepository implements ClinicInventoryRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $clinicInventories = ClinicInventory::query();

        return datatables()->of($clinicInventories)
            ->editColumn('main_image', function ($item) {
                return '<img src="' . $item->main_image . '" alt="" class="img-fluid" style="width: 50px; height: 50px;">';
            })
            ->addColumn('action', fn($item) => $this->clinicInventoryActions($item))
            ->addColumn('movements', fn($item) => $this->clinicInventoryMovements($item))
            ->rawColumns(['action', 'main_image', 'movements'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveClinicInventory(new ClinicInventory(), $request, 'created');
    }

    public function show($id)
    {
        return ClinicInventory::findOrFail($id);
    }

    public function update($request, $id)
    {
        $clinicInventory = ClinicInventory::findOrFail($id);
        return $this->saveClinicInventory($clinicInventory, $request, 'updated');
    }

    public function destroy($id)
    {
        $clinicInventory = ClinicInventory::findOrFail($id);
        $clinicInventory->delete();

        return $this->jsonResponse('success', __('Clinic inventory deleted successfully'));
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $clinicInventories = ClinicInventory::onlyTrashed()->get();

        return datatables()->of($clinicInventories)
            ->addColumn('trash_action', fn($item) => $this->clinicInventoryTrashActions($item))
            ->rawColumns(['trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $clinicInventory = ClinicInventory::onlyTrashed()->findOrFail($id);
        $clinicInventory->restore();

        return $this->jsonResponse('success', __('Clinic inventory restored successfully'));
    }

    public function forceDelete($id)
    {
        $clinicInventory = ClinicInventory::onlyTrashed()->findOrFail($id);
        $clinicInventory->forceDelete();


        return $this->jsonResponse('success', __('Clinic inventory deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveClinicInventory($clinicInventory, $request, string $action)
    {
        try {
            DB::beginTransaction();
            $clinicInventory->fill($request->validated())->save();

            // Media
            if ($request->hasFile('main_image') || $request->hasFile('images')) {
                $this->processMedia($clinicInventory, $request, [
                    ['field' => 'main_image', 'collection' => 'main_image', 'multiple' => false],
                    ['field' => 'images', 'collection' => 'clinic_inventory_images', 'multiple' => true],
                ], $action);
            }


            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Clinic inventory ' . $action . ' successfully'));
            }
            DB::commit();

            return redirect()->route('clinic.clinic-inventories.index')->with('success', __('Clinic inventory ' . $action . ' successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function clinicInventoryActions($item): string
    {
        $editUrl = route('clinic.clinic-inventories.edit', $item->id);
        $showUrl = route('clinic.clinic-inventories.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
           <a href="{$editUrl}" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></a>
           <button onclick="deleteClinicInventory({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function clinicInventoryMovements($item): string
    {
        return '<a href="' . route('clinic.clinic-inventory-movements.index', $item->id) . '" class="btn btn-sm btn-info">
        ' . __('Movements') . '
        </a>';
    }

    private function clinicInventoryTrashActions($item): string
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
