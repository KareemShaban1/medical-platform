<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\SupplierRepositoryInterface;
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
            ->addColumn('approval', fn($item) => $this->supplierApproval($item))
            ->addColumn('action', fn($item) => $this->supplierActions($item))
            ->rawColumns(['status', 'is_allowed', 'action', 'approval'])
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

        // fallback to "status" if field is not sent
        $field = $request->field ?? 'status';
        $value = (bool)$request->value;

        $supplier->{$field} = $value;
        $supplier->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Supplier status updated successfully'),
        ]);
    }

    public function updateIsAllowed($request)
    {
        $supplier = Supplier::findOrFail($request->id);

        // fallback to "is_allowed" if field is not sent
        $field = $request->field ?? 'is_allowed';
        $value = (bool)$request->value;

        $supplier->{$field} = $value;
        $supplier->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Supplier is allowed updated successfully'),
        ]);
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
            <input type="checkbox" 
                   class="form-check-input toggle-boolean-status" 
                   data-id="{$item->id}" 
                   data-field="status" 
                   value="1" {$checked}>
        </div>
        HTML;
    }

    private function supplierIsAllowed($item): string
    {
        $checked = $item->is_allowed ? 'checked' : '';
        return <<<HTML
        <div class="form-check form-switch mt-2">
            <input type="checkbox" 
                   class="form-check-input toggle-boolean-is-allowed" 
                   data-id="{$item->id}" 
                   data-field="is_allowed" 
                   value="1" {$checked}>
        </div>
        HTML;
    }

    private function supplierApproval($item): string
    {
        $approved = $item->approvement?->action;

        $badgeClass = match ($approved) {
            'under_review' => 'bg-warning',
            'approved'     => 'bg-success',
            'rejected'     => 'bg-danger',
            default        => 'bg-secondary',
        };

        $label = $approved ?? 'pending';
        $approvalId = $item->approvement?->id ?? 'null';

        return <<<HTML
            <div>
                <span class="badge {$badgeClass}">{$label}</span>
                <br>
                <button class="btn btn-sm btn-primary" onclick="changeApproval({$item->id}, {$approvalId})">
                    Change Approval
                </button>
            </div>
        HTML;
    }


    private function supplierActions($item): string
    {
        $productsUrl = route('admin.supplier-products.by-supplier', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="showSupplier({$item->id})" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i></button>
            <a href="{$productsUrl}" class="btn btn-sm btn-success" title="View Products"><i class="fa fa-box"></i></a>
            <button onclick="editSupplier({$item->id})" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-edit"></i></button>
            <button onclick="deleteSupplier({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
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
