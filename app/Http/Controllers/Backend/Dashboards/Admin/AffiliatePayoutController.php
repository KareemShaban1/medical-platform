<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliatePayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliatePayoutController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!hasPermission('view affiliate payouts'), 403, __('You are not authorized to view affiliate payouts'));

        $requests = AffiliatePayoutRequest::with(['affiliateCode.affiliateable', 'paidByAdmin'])
            ->latest()
            ->paginate(20);

        return view('backend.dashboards.admin.pages.affiliates.payouts', compact('requests'));
    }

    public function markPaid(Request $request, AffiliatePayoutRequest $payoutRequest)
    {
        abort_if(!hasPermission('update affiliate payouts'), 403, __('You are not authorized to update affiliate payouts'));

        if ($payoutRequest->status !== 'pending') {
            return redirect()->back()->with('error', __('This payout request has already been processed.'));
        }

        DB::transaction(function () use ($payoutRequest) {
            $code = $payoutRequest->affiliateCode;
            if ($code) {
                $code->balance = max(0, (float) $code->balance - (float) $payoutRequest->amount);
                $code->save();
            }

            $payoutRequest->update([
                'status' => 'paid',
                'paid_at' => now(),
                'paid_by_admin_id' => auth('admin')->id(),
            ]);
        });

        return redirect()->back()->with('success', __('Payout request marked as paid.'));
    }
}
