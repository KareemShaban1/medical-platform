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
        return view('backend.dashboards.clinic.pages.salary-contracts.index');
    }

    public function data()
    {
        return $this->salaryContractRepo->data();
    }

    // create
    public function create()
    {
        $clinicUsers = ClinicUser::forClinic()->get();
        return view('backend.dashboards.clinic.pages.salary-contracts.create', compact('clinicUsers'));
    }

    public function store(StoreSalaryContractRequest $request)
    {
        return $this->salaryContractRepo->store($request);
    }

    public function show($id)
    {
        $salaryContract = $this->salaryContractRepo->show($id);

        return request()->ajax()
            ? response()->json($salaryContract)
            : view('backend.dashboards.clinic.pages.salary-contracts.show', compact('salaryContract'));
    }

    public function edit($id){
        $salaryContract = $this->salaryContractRepo->show($id);
        $clinicUsers = ClinicUser::forClinic()->get();
        return view('backend.dashboards.clinic.pages.salary-contracts.edit', compact('salaryContract', 'clinicUsers'));

    }

    public function update(UpdateSalaryContractRequest $request, $id)
    {
        return $this->salaryContractRepo->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->salaryContractRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.salary-contracts.trash');
    }

    public function trashData()
    {
        return $this->salaryContractRepo->trashData();
    }


    public function restore($id)
    {
        return $this->salaryContractRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->salaryContractRepo->forceDelete($id);
    }
}
