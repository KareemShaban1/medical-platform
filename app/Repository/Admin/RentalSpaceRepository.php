<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\RentalSpaceRepositoryInterface;
use App\Models\ModuleApprovement;
use App\Models\RentalSpace;
use App\Traits\HandlesMediaUploads;

class RentalSpaceRepository implements RentalSpaceRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $rentalSpaces = RentalSpace::query();

        return datatables()->of($rentalSpaces)
            ->addColumn('approval', fn($item) => $this->rentalSpaceApproval($item))
            ->editColumn('status', fn($item) => $this->rentalSpaceStatus($item))
            ->addColumn('action', fn($item) => $this->rentalSpaceActions($item))
            ->rawColumns(['status', 'action', 'approval'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveRentalSpace(new RentalSpace(), $request, 'created');
    }

    public function show($id)
    {
        return RentalSpace::with(['approvement','availability','pricing','booking'])->findOrFail($id);
    }

    public function update($request, $id)
    {
        $rentalSpace = RentalSpace::findOrFail($id);
        return $this->saveRentalSpace($rentalSpace, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $rentalSpace = RentalSpace::findOrFail($request->id);

        // fallback to "status" if field is not sent
        $field = $request->field ?? 'status';
        $value = (bool)$request->value;

        $rentalSpace->{$field} = $value;
        $rentalSpace->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Rental space status updated successfully'),
        ]);
    }


    public function destroy($id)
    {
        $rentalSpace = RentalSpace::findOrFail($id);
        // Clear media collections before delete
        $this->clearAllMedia($rentalSpace, ['main_image', 'rental_space_images']);
        $rentalSpace->delete();
        $rentalSpace->availability()->delete();
        $rentalSpace->pricing()->delete();
        $rentalSpace->booking()->delete();

        return $this->jsonResponse('success', __('Rental space deleted successfully'));
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $rentalSpaces = RentalSpace::onlyTrashed()->get();

        return datatables()->of($rentalSpaces)
            ->editColumn('status', fn($item) => $this->rentalSpaceStatus($item))
            ->addColumn('trash_action', fn($item) => $this->rentalSpaceTrashActions($item))
            ->rawColumns(['status', 'trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $rentalSpace = RentalSpace::onlyTrashed()->findOrFail($id);
        $rentalSpace->restore();
        $rentalSpace->availability()->restore();
        $rentalSpace->pricing()->restore();
        $rentalSpace->booking()->restore();

        return $this->jsonResponse('success', __('Rental space restored successfully'));
    }

    public function forceDelete($id)
    {
        $rentalSpace = RentalSpace::onlyTrashed()->findOrFail($id);
        $rentalSpace->forceDelete();
        $rentalSpace->availability()->forceDelete();
        $rentalSpace->pricing()->forceDelete();
        $rentalSpace->booking()->forceDelete();

        return $this->jsonResponse('success', __('Rental space deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveRentalSpace($rentalSpace, $request, string $action)
    {
        try {
            $rentalSpace->fill($request->validated())->save();

            if ($action == 'created') {
                ModuleApprovement::create([
                    'module_id' => $rentalSpace->id,
                    'module_type' => RentalSpace::class,
                    'status' => 'under_review',
                ]);
            }


            // Media
            if ($request->hasFile('main_image') || $request->hasFile('images')) {
                $this->processMedia($rentalSpace, $request, [
                    ['field' => 'main_image', 'collection' => 'main_image', 'multiple' => false],
                    ['field' => 'images', 'collection' => 'rental_space_images', 'multiple' => true],
                ], $action);
            }

            // Availability (one-to-one)
            if ($request->has('availability')) {
                $rentalSpace->availability()->updateOrCreate([
                    'rental_space_id' => $rentalSpace->id,
                ], $request->availability);
            }

            // Pricing (one-to-one)
            if ($request->has('pricing')) {
                $rentalSpace->pricing()->updateOrCreate([
                    'rental_space_id' => $rentalSpace->id,
                ], $request->pricing);
            }

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Rental space ' . $action . ' successfully'));
            }

            return redirect()->route('admin.rental-spaces.index')->with('success', __('Rental space ' . $action . ' successfully'));
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function rentalSpaceApproval($item): string
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


    private function rentalSpaceStatus($item): string
    {
        $checked = $item->status ? 'checked' : '';
        return <<<HTML
        <div class="form-check form-switch mt-2">
            <input type="checkbox" 
                   class="form-check-input toggle-boolean" 
                   data-id="{$item->id}" 
                   data-field="status" 
                   value="1" {$checked}>
        </div>
        HTML;
    }


    private function rentalSpaceActions($item): string
    {
        // $editUrl = route('admin.rental-spaces.edit', $item->id);
        $showUrl = route('admin.rental-spaces.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
        </div>
        HTML;
    }

    private function rentalSpaceTrashActions($item): string
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
