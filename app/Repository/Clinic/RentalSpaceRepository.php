<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\RentalSpaceRepositoryInterface;
use App\Models\ModuleApprovement;
use App\Models\Admin;
use App\Notifications\Admin\RentalSpaceSubmittedForReview;
use App\Models\RentalSpace;
use App\Traits\HandlesMediaUploads;
use Illuminate\Support\Facades\DB;

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
        $rentalSpaces = RentalSpace::forCurrentClinic();

        return datatables()->of($rentalSpaces)
            ->editColumn('status', fn($item) => $this->rentalSpaceStatus($item))
            ->addColumn('action', fn($item) => $this->rentalSpaceActions($item))
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveRentalSpace(new RentalSpace(), $request, 'created');
    }

    public function show($id)
    {
        return RentalSpace::findOrFail($id);
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
            DB::beginTransaction();
            $rentalSpace->fill($request->validated())->save();

            if ($action == 'created') {
                $adminId = Admin::query()->value('id');
                if ($adminId) {
                    ModuleApprovement::create([
                        'module_id' => $rentalSpace->id,
                        'module_type' => RentalSpace::class,
                        'action' => 'under_review',
                        'action_by' => $adminId,
                    ]);
                }

                // Notify all admins that a new rental space was submitted
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    $admin->notify(new RentalSpaceSubmittedForReview($rentalSpace));
                }
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

            DB::commit();

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Rental space ' . $action . ' successfully'));
            }

            return redirect()->route('clinic.rental-spaces.index')->with('success', __('Rental space ' . $action . ' successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse('error', $e->getMessage());
        }

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
        $html = '<div class="d-flex gap-2">';
        
        if (hasPermission('view rental spaces')) {
            $showUrl = route('clinic.rental-spaces.show', $item->id);
            $html .= '<a href="' . $showUrl . '" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        }
        
        if (hasPermission('update rental space')) {
            $editUrl = route('clinic.rental-spaces.edit', $item->id);
            $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></a>';
        }
        
        if (hasPermission('delete rental space')) {
            $html .= '<button onclick="deleteRentalSpace(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function rentalSpaceTrashActions($item): string
    {
        $html = '<div class="d-flex gap-2">';
        
        if (hasPermission('restore rental space')) {
            $html .= '<button onclick="restore(' . $item->id . ')" class="btn btn-sm btn-info" title="Restore"><i class="fa fa-undo"></i></button>';
        }
        
        if (hasPermission('force delete rental space')) {
            $html .= '<button onclick="forceDelete(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';
        }
        
        $html .= '</div>';
        
        return $html;
    }


    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
