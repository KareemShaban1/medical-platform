<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\ClinicRepositoryInterface;
use App\Models\Clinic;

class ClinicRepository implements ClinicRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $clinics = Clinic::query();

        return datatables()->of($clinics)
            ->addColumn('clinic_users', fn($item) => $item->clinicUsers->count())
            ->editColumn('status', fn($item) => $this->clinicStatus($item))
            ->editColumn('is_allowed', fn($item) => $this->clinicIsAllowed($item))
            ->addColumn('action', fn($item) => $this->clinicActions($item))
            ->rawColumns(['status', 'is_allowed', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveClinic(new Clinic(), $request, 'created');
    }

    public function show($id)
    {
        return Clinic::findOrFail($id);
    }

    public function update($request, $id)
    {
        $clinic = Clinic::findOrFail($id);
        return $this->saveClinic($clinic, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $clinic = Clinic::findOrFail($request->id);
        $clinic->status = (bool)$request->status;
        $clinic->save();

        return $this->jsonResponse('success', __('Clinic status updated successfully'));
    }

    public function destroy($id)
    {
        $clinic = Clinic::findOrFail($id);
        $clinic->delete();

        return $this->jsonResponse('success', __('Clinic deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveClinic($clinic, $request, string $action)
    {
        try {
            $clinic->fill($request->validated())->save();

            if ($request->hasFile('images')) {
                if($action == 'updated') {
                    // Remove old images
                    $clinic->clearMediaCollection('clinic_images');
                }
                foreach ($request->file('images') as $image) {
                    $clinic->addMedia($image)->toMediaCollection('clinic_images');
                }
            }

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Clinic '.$action.' successfully'));
            }

            return redirect()->route('admin.clinics.index')->with('success', __('Clinic '.$action.' successfully'));
        } catch (\Throwable $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function clinicStatus($item): string
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

    private function clinicIsAllowed($item): string
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


    private function clinicActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="showClinic({$item->id})" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button>
            <button onclick="editClinic({$item->id})" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></button>
            <button onclick="deleteClinic({$item->id})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
