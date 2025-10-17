<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\Payslip\StorePayslipRequest;
use App\Http\Requests\Clinic\Payslip\UpdatePayslipRequest;
use App\Interfaces\Clinic\PayslipRepositoryInterface;
use App\Models\ClinicUser;
use App\Models\SalaryContract;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    protected $payslipRepo;

    public function __construct(PayslipRepositoryInterface $payslipRepo)
    {
        $this->payslipRepo = $payslipRepo;
    }

    public function index()
    {
        return view('backend.dashboards.clinic.pages.payslips.index');
    }

    public function data(Request $request)
    {
        return $this->payslipRepo->data($request);
    }

    // create 
    public function create($userId)
    {
        $clinic_user = ClinicUser::findOrFail($userId);
        // get active salary contract for this user
        $contract = SalaryContract::where('clinic_user_id', $clinic_user->id)
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->latest('effective_from')
            ->first();
        return view('backend.dashboards.clinic.pages.payslips.create', compact('clinic_user', 'contract'));
    }

    public function store(StorePayslipRequest $request)
    {
        return $this->payslipRepo->store($request);
    }

    public function show($id)
    {
        $payslip = $this->payslipRepo->show($id);

        return request()->ajax()
            ? response()->json($payslip)
            : view('backend.dashboards.clinic.pages.payslips.show', compact('payslip'));
    }

    public function edit($id)
    {

        $payslip = $this->payslipRepo->show($id);
        $contract = $payslip->clinicUser->salaryContract()
            ->where('effective_from', '<=', $payslip->period_start)
            ->where(function ($q) use ($payslip) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', $payslip->period_end);
            })
            ->latest('effective_from')
            ->first();
        return view('backend.dashboards.clinic.pages.payslips.edit', compact('payslip', 'contract'));
    }

    public function update(UpdatePayslipRequest $request, $id)
    {
        return $this->payslipRepo->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->payslipRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.salary-contracts.trash');
    }

    public function trashData()
    {
        return $this->payslipRepo->trashData();
    }


    public function restore($id)
    {
        return $this->payslipRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->payslipRepo->forceDelete($id);
    }
}
