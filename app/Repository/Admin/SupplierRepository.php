<?php

namespace App\Repository\Admin;

use App\Models\Supplier;

class SupplierRepository implements SupplierRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $suppliers = Supplier::query();

        return datatables()->of($suppliers)
            ->addColumn('supplier_users', fn($item) => $item->supplierUsers->count())
            ->editColumn('status', fn($item) => $this->supplierStatus($item))
            ->editColumn('is_allowed', fn($item) => $this->supplierIsAllowed($item))
            ->addColumn('action', fn($item) => $this->supplierActions($item))
            ->rawColumns(['status', 'is_allowed', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveSupplier(new Supplier(), $request, 'created');
    }

    public function show($id)
    {
        return Supplier::findOrFail($id);
    }

    public function update($request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        return $this->saveSupplier($supplier, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $supplier = Supplier::findOrFail($request->id);
        $supplier->status = (bool)$request->status;
        $supplier->save();

        return $this->jsonResponse('success', __('Supplier status updated successfully'));
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return $this->jsonResponse('success', __('Supplier deleted successfully'));
    }

   
    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveSupplier($supplier, $request, string $action)
    {
        try {
            $supplier->fill($request->validated())->save();

            if ($request->hasFile('images')) {
                if($action == 'updated') {
                    // Remove old images
                    $supplier->clearMediaCollection('supplier_images');
                }
                foreach ($request->file('images') as $image) {
                    $supplier->addMedia($image)->toMediaCollection('supplier_images');
                }
            }


            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Supplier '.$action.' successfully'));
            }

            return redirect()->route('admin.suppliers.index')->with('success', __('Supplier '.$action.' successfully'));
        } catch (\Throwable $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function supplierStatus($item): string
    {
        $checked = $item->status ? 'checked' : '';
        return <<<HTML
            <div class="form-check form-switch mt-2">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" class="form-check-input toggle-boolean"
                       data-id="{$item->id}" data-field="status" id="status-{$item->id}"
                       name="status" value="1" {$checked}>
            </div>
        HTML;
    }

    private function supplierIsAllowed($item): string
    {
        $checked = $item->is_allowed ? 'checked' : '';
        return <<<HTML
            <div class="form-check form-switch mt-2">
                <input type="hidden" name="is_allowed" value="0">
                <input type="checkbox" class="form-check-input toggle-boolean"
                       data-id="{$item->id}" data-field="is_allowed" id="is_allowed-{$item->id}"
                       name="is_allowed" value="1" {$checked}>
            </div>
        HTML;
    } 


    private function supplierActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="showSupplier({$item->id})" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button>
            <button onclick="editSupplier({$item->id})" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></button>
            <button onclick="deleteSupplier({$item->id})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
