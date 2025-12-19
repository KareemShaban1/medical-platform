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
        // apply permissions
        abort_if(!hasPermission('view payslips'), 403, __('You are not authorized to view payslips'));

        return view('backend.dashboards.clinic.pages.payslips.index');
    }

    public function data(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view payslips'), 403, __('You are not authorized to view payslips'));

        return $this->payslipRepo->data($request);
    }

    // create
    public function create($userId)
    {
        // apply permissions
        abort_if(!hasPermission('create payslip'), 403, __('You are not authorized to create payslip'));

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
        // apply permissions
        abort_if(!hasPermission('create payslip'), 403, __('You are not authorized to create payslip'));

        return $this->payslipRepo->store($request);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view payslips'), 403, __('You are not authorized to view payslip'));

        $payslip = $this->payslipRepo->show($id);

        return request()->ajax()
            ? response()->json($payslip)
            : view('backend.dashboards.clinic.pages.payslips.show', compact('payslip'));
    }

    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update payslip'), 403, __('You are not authorized to update payslip'));

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
        // apply permissions
        abort_if(!hasPermission('update payslip'), 403, __('You are not authorized to update payslip'));

        return $this->payslipRepo->update($request, $id);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete payslip'), 403, __('You are not authorized to delete payslip'));

        return $this->payslipRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash payslips'), 403, __('You are not authorized to view trash payslips'));

        return view('backend.dashboards.clinic.pages.payslips.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash payslips'), 403, __('You are not authorized to view trash payslips'));

        return $this->payslipRepo->trashData();
    }


    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore payslip'), 403, __('You are not authorized to restore payslip'));

        return $this->payslipRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete payslip'), 403, __('You are not authorized to force delete payslip'));

        return $this->payslipRepo->forceDelete($id);
    }
}
