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

        $validated = $request->validate([
            'admin_note' => 'nullable|string|max:2000',
            'proof_images' => 'nullable|array',
            'proof_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        DB::transaction(function () use ($payoutRequest, $validated, $request) {
            $code = $payoutRequest->affiliateCode;
            if ($code) {
                $code->balance = max(0, (float) $code->balance - (float) $payoutRequest->amount);
                $code->save();
            }

            $payoutRequest->update([
                'status' => 'paid',
                'paid_at' => now(),
                'paid_by_admin_id' => auth('admin')->id(),
                'admin_note' => $validated['admin_note'] ?? null,
            ]);

            if ($request->hasFile('proof_images')) {
                foreach ($request->file('proof_images', []) as $image) {
                    $payoutRequest
                        ->addMedia($image)
                        ->toMediaCollection('affiliate_payout_proofs');
                }
            }
        });

        return redirect()->back()->with('success', __('Payout request marked as paid.'));
    }
}
