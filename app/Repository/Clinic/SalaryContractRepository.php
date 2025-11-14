<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\SalaryContractRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Traits\HandlesMediaUploads;
use App\Models\SalaryContract;
use Yajra\DataTables\Facades\DataTables;

class SalaryContractRepository implements SalaryContractRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $salaryContracts = SalaryContract::forCurrentClinic()->with('clinicUser');


        return datatables()->of($salaryContracts)
            ->addColumn('user', fn($item) => $item->clinicUser->name)
            ->addColumn('action', fn($item) => $this->salaryContractActions($item))
            ->filterColumn('user', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->whereHas('clinicUser', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                });
            })
            ->rawColumns(['action', 'user'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveSalaryContract(new SalaryContract(), $request, 'created');
    }

    public function show($id)
    {
        return SalaryContract::findOrFail($id);
    }

    public function update($request, $id)
    {
        $salaryContract = SalaryContract::findOrFail($id);
        return $this->saveSalaryContract($salaryContract, $request, 'updated');
    }

    public function destroy($id)
    {
        $salaryContract = SalaryContract::findOrFail($id);
        $salaryContract->delete();

        return $this->jsonResponse('success', __('Salary contract deleted successfully'));
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $salaryContracts = SalaryContract::onlyTrashed()->get();

        return datatables()->of($salaryContracts)
            ->addColumn('user', fn($item) => $item->clinicUser->name)
            ->addColumn('trash_action', fn($item) => $this->salaryContractTrashActions($item))
            ->rawColumns(['trash_action', 'user'])
            ->make(true);
    }

    public function restore($id)
    {
        $salaryContract = SalaryContract::onlyTrashed()->findOrFail($id);
        $salaryContract->restore();

        return $this->jsonResponse('success', __('Salary contract restored successfully'));
    }

    public function forceDelete($id)
    {
        $salaryContract = SalaryContract::onlyTrashed()->findOrFail($id);
        $salaryContract->forceDelete();


        return $this->jsonResponse('success', __('Salary contract deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveSalaryContract($salaryContract, $request, string $action)
    {
        try {
            DB::beginTransaction();
            $salaryContract->fill($request->validated())->save();

            // Media
            if ($request->hasFile('images')) {
                $this->processMedia($salaryContract, $request, [
                    ['field' => 'images', 'collection' => 'salary_contract_images', 'multiple' => true],
                ], $action);
            }

            DB::commit();

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Salary contract ' . $action . ' successfully'));
            }

            return redirect()->route('clinic.salary-contracts.index')->with('success', __('Salary contract ' . $action . ' successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function salaryContractActions($item): string
    {
        $editUrl = route('clinic.salary-contracts.edit', $item->id);
        $showUrl = route('clinic.salary-contracts.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
           <a href="{$editUrl}" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></a>
           <button onclick="deleteSalaryContract({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }


    private function salaryContractTrashActions($item): string
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