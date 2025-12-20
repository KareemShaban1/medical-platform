<?php

namespace App\Http\Controllers\Backend\Dashboards\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliatePayoutProfile;
use App\Models\AffiliatePayoutRequest;
use App\Models\Admin;
use App\Notifications\Admin\AffiliatePayoutRequestedNotification;
use App\Services\Affiliate\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PayoutRequestController extends Controller
{
    public function store(Request $request, AffiliateService $affiliateService)
    {
        $user = auth('affiliate')->user();
        $code = $user->affiliateCode ?: $affiliateService->ensureCode($user);

        if (!$code || $code->balance <= 0) {
            return redirect()->back()->with('error', __('Your balance is not eligible for payout yet.'));
        }

        $hasPending = AffiliatePayoutRequest::where('affiliate_code_id', $code->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect()->back()->with('error', __('You already have a pending payout request.'));
        }

        $validated = $request->validate([
            'payout_method' => 'required|string|max:100',
            'payout_details' => 'required|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);

        $requestModel = DB::transaction(function () use ($code, $validated) {
            AffiliatePayoutProfile::updateOrCreate(
                ['affiliate_code_id' => $code->id],
                [
                    'payout_method' => $validated['payout_method'],
                    'payout_details' => $validated['payout_details'],
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            return AffiliatePayoutRequest::create([
                'affiliate_code_id' => $code->id,
                'amount' => $code->balance,
                'payout_method' => $validated['payout_method'],
                'payout_details' => $validated['payout_details'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);
        });

        $admins = Admin::all();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AffiliatePayoutRequestedNotification($requestModel));
        }

        return redirect()->back()->with('success', __('Payout request submitted successfully.'));
    }
}
