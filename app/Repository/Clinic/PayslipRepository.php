<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\PayslipRepositoryInterface;
use App\Models\ClinicUser;
use Illuminate\Support\Facades\DB;
use App\Traits\HandlesMediaUploads;
use App\Models\Payslip;
use App\Models\PayslipItem;

class PayslipRepository implements PayslipRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data($request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $startOfMonth = $month . '-01';
        $endOfMonth = date('Y-m-t', strtotime($startOfMonth));

        $clinicUsers = ClinicUser::with(['payslips' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->where(function ($q) use ($startOfMonth, $endOfMonth) {
                // overlap between payslip period and selected month
                $q->whereBetween('period_start', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('period_end', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                        $q2->where('period_start', '<=', $startOfMonth)
                            ->where('period_end', '>=', $endOfMonth);
                    });
            });
        }])->get();

        return datatables()->of($clinicUsers)
            ->addColumn('name', fn($user) => $user->name)
            ->addColumn('has_payslip', fn($user) => $user->payslips->isNotEmpty() ? 'Yes' : 'No')
            ->addColumn('action', function ($user) use ($month) {
                if ($user->payslips->isNotEmpty()) {
                    $payslipId = $user->payslips->first()->id;
                    return '<button class="btn btn-sm btn-warning edit-payslip" data-id="' . $payslipId . '">
                                Edit
                            </button>';
                }
                return '<button class="btn btn-sm btn-success add-payslip" data-user-id="' . $user->id . '">
                            Add
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store($request)
    {
        try {
            DB::transaction(function () use ($request) {
                $gross = collect($request->items)
                    ->whereIn('type', ['fixed', 'hours', 'percentage', 'bonus'])
                    ->sum('amount');

                $deductions = collect($request->items)
                    ->where('type', 'deduction')
                    ->sum('amount');

                $net = $gross - $deductions;

                $payslip = Payslip::create([
                    'clinic_user_id' => $request->clinic_user_id,
                    'period_start' => $request->period_start,
                    'period_end' => $request->period_end,
                    'gross_amount' => $gross,
                    'deductions' => $deductions,
                    'net_amount' => $net,
                    'status' => $request->status,
                    'paid_at' => $request->status === 'paid' ? now() : null,
                ]);

                foreach ($request->items as $item) {
                    PayslipItem::create([
                        'payslip_id' => $payslip->id,
                        'type' => $item['type'],
                        'notes' => $item['notes'],
                        'amount' => $item['amount'],
                    ]);
                }
            });
            DB::commit();



            return redirect()->route('clinic.payslips.index')->with('success', __('Payslip created successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        return Payslip::with(['clinicUser', 'items'])->findOrFail($id);
    }

    public function update($request, $id)
    {
        try {
            $payslip = Payslip::findOrFail($id);
            DB::transaction(function () use ($payslip, $request) {
                $gross = collect($request->items)
                    ->whereIn('type', ['fixed', 'hours', 'percentage', 'bonus'])
                    ->sum('amount');
                $deductions = collect($request->items)
                    ->where('type', 'deduction')
                    ->sum('amount');
                $net = $gross - $deductions;

                $payslip->update([
                    'period_start' => $request->period_start,
                    'period_end' => $request->period_end,
                    'gross_amount' => $gross,
                    'deductions' => $deductions,
                    'net_amount' => $net,
                    'status' => $request->status,
                ]);

                // Refresh items
                $payslip->items()->delete();

                foreach ($request->items as $item) {
                    $payslip->items()->create([
                        'type' => $item['type'],
                        'notes' => $item['notes'],
                        'amount' => $item['amount'],
                    ]);
                }
            });

            return redirect()->route('clinic.payslips.index')->with('success', __('Payslip updated successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $payslip = Payslip::findOrFail($id);
        $payslip->delete();

        return $this->jsonResponse('success', __('Payslip deleted successfully'));
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $payslips = Payslip::onlyTrashed()->get();

        return datatables()->of($payslips)
            ->addColumn('user', fn($item) => $item->clinicUser->name)
            ->addColumn('trash_action', fn($item) => $this->payslipTrashActions($item))
            ->rawColumns(['trash_action', 'user'])
            ->make(true);
    }

    public function restore($id)
    {
        $payslip = Payslip::onlyTrashed()->findOrFail($id);
        $payslip->restore();

        return $this->jsonResponse('success', __('Payslip restored successfully'));
    }

    public function forceDelete($id)
    {
        $payslip = Payslip::onlyTrashed()->findOrFail($id);
        $payslip->forceDelete();


        return $this->jsonResponse('success', __('Payslip deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */


    private function payslipActions($item): string
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


    private function payslipTrashActions($item): string
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