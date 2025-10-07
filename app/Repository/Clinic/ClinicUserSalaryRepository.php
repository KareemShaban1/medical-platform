<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\ClinicUserSalaryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Traits\HandlesMediaUploads;
use App\Models\ClinicUserSalary;

class ClinicUserSalaryRepository implements ClinicUserSalaryRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $clinicUserSalaries = ClinicUserSalary::query();

        return datatables()->of($clinicUserSalaries)
            ->addColumn('user', fn($item) => $item->clinicUser->name)
            ->addColumn('paid', fn($item) => $this->getPaidBadge($item))
            ->addColumn('action', fn($item) => $this->clinicUserSalaryActions($item))
            ->rawColumns(['action', 'user', 'paid'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveClinicUserSalary(new ClinicUserSalary(), $request, 'created');
    }

    public function show($id)
    {
        return ClinicUserSalary::findOrFail($id);
    }

    public function update($request, $id)
    {
        $clinicUserSalary = ClinicUserSalary::findOrFail($id);
        return $this->saveClinicUserSalary($clinicUserSalary, $request, 'updated');
    }

    public function destroy($id)
    {
        $clinicUserSalary = ClinicUserSalary::findOrFail($id);
        $clinicUserSalary->delete();

        return $this->jsonResponse('success', __('Clinic user salary deleted successfully'));
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $clinicUserSalaries = ClinicUserSalary::onlyTrashed()->get();

        return datatables()->of($clinicUserSalaries)
            ->addColumn('user', fn($item) => $item->clinicUser->name)
            ->addColumn('paid', fn($item) => $this->getPaidBadge($item))
            ->addColumn('trash_action', fn($item) => $this->clinicUserSalaryTrashActions($item))
            ->rawColumns(['trash_action', 'user', 'paid'])
            ->make(true);
    }

    public function restore($id)
    {
        $clinicUserSalary = ClinicUserSalary::onlyTrashed()->findOrFail($id);
        $clinicUserSalary->restore();

        return $this->jsonResponse('success', __('Clinic user salary restored successfully'));
    }

    public function forceDelete($id)
    {
        $clinicUserSalary = ClinicUserSalary::onlyTrashed()->findOrFail($id);
        $clinicUserSalary->forceDelete();


        return $this->jsonResponse('success', __('Clinic user salary deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveClinicUserSalary($clinicUserSalary, $request, string $action)
    {
        try {
            DB::beginTransaction();
            $clinicUserSalary->fill($request->validated())->save();

            // Media
            if ($request->hasFile('images')) {
                $this->processMedia($clinicUserSalary, $request, [
                    ['field' => 'images', 'collection' => 'clinic_user_salary_images', 'multiple' => true],
                ], $action);
            }


            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Clinic inventory ' . $action . ' successfully'));
            }
            DB::commit();

            return redirect()->route('clinic.clinic-user-salaries.index')->with('success', __('Clinic user salary ' . $action . ' successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function clinicUserSalaryActions($item): string
    {
        $editUrl = route('clinic.clinic-user-salaries.edit', $item->id);
        $showUrl = route('clinic.clinic-user-salaries.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
           <a href="{$editUrl}" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></a>
           <button onclick="deleteClinicUserSalary({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }


    private function clinicUserSalaryTrashActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
           <button onclick="restore({$item->id})" class="btn btn-sm btn-info" title="Restore"><i class="fa fa-undo"></i></button>
           <button onclick="forceDelete({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function getPaidBadge($item): string
    {
        return $item->paid ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-danger">Unpaid</span>';
    }


    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}