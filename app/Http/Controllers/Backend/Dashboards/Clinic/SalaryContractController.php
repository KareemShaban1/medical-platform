<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\SalaryContract;
use App\Http\Requests\Clinic\SalaryContract\StoreSalaryContractRequest;
use App\Http\Requests\Clinic\SalaryContract\UpdateSalaryContractRequest;
use App\Interfaces\Clinic\SalaryContractRepositoryInterface;
use App\Models\ClinicUser;

class SalaryContractController extends Controller
{
    protected $salaryContractRepo;

    public function __construct(SalaryContractRepositoryInterface $salaryContractRepo)
    {
        $this->salaryContractRepo = $salaryContractRepo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view salary contracts'), 403, __('You are not authorized to view salary contracts'));

        return view('backend.dashboards.clinic.pages.salary-contracts.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view salary contracts'), 403, __('You are not authorized to view salary contracts'));

        return $this->salaryContractRepo->data();
    }

    // create
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create salary contract'), 403, __('You are not authorized to create salary contract'));

        $clinicUsers = ClinicUser::forClinic()->get();
        return view('backend.dashboards.clinic.pages.salary-contracts.create', compact('clinicUsers'));
    }

    public function store(StoreSalaryContractRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create salary contract'), 403, __('You are not authorized to create salary contract'));

        return $this->salaryContractRepo->store($request);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view salary contract'), 403, __('You are not authorized to view salary contract'));

        $salaryContract = $this->salaryContractRepo->show($id);

        return request()->ajax()
            ? response()->json($salaryContract)
            : view('backend.dashboards.clinic.pages.salary-contracts.show', compact('salaryContract'));
    }

    public function edit($id){
        // apply permissions
        abort_if(!hasPermission('update salary contract'), 403, __('You are not authorized to edit salary contract'));

        $salaryContract = $this->salaryContractRepo->show($id);
        $clinicUsers = ClinicUser::forClinic()->get();
        return view('backend.dashboards.clinic.pages.salary-contracts.edit', compact('salaryContract', 'clinicUsers'));

    }

    public function update(UpdateSalaryContractRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update salary contract'), 403, __('You are not authorized to update salary contract'));

        return $this->salaryContractRepo->update($request, $id);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete salary contract'), 403, __('You are not authorized to delete salary contract'));

        return $this->salaryContractRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash salary contracts'), 403, __('You are not authorized to view trash salary contracts'));

        return view('backend.dashboards.clinic.pages.salary-contracts.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash salary contracts'), 403, __('You are not authorized to view trash salary contracts'));

        return $this->salaryContractRepo->trashData();
    }


    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore salary contract'), 403, __('You are not authorized to restore salary contract'));

        return $this->salaryContractRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete salary contract'), 403, __('You are not authorized to force delete salary contract'));

        return $this->salaryContractRepo->forceDelete($id);
    }
}
