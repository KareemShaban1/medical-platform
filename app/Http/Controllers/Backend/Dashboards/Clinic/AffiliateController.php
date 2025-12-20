<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\AffiliatePayoutProfile;
use App\Models\AffiliatePayoutRequest;
use App\Models\AffiliateTransaction;
use App\Services\Affiliate\AffiliateService;

class AffiliateController extends Controller
{
    public function index(AffiliateService $affiliateService)
    {
        $user = auth('clinic')->user();
        $code = $user->affiliateCode ?: $affiliateService->ensureCode($user);
        $settings = $affiliateService->getSettings();

        $discountPercent = $code?->discount_percent ?? $settings->default_discount_percent;
        $commissionPercent = $code?->commission_percent ?? $settings->default_commission_percent;

        $transactions = $code
            ? AffiliateTransaction::where('affiliate_code_id', $code->id)->latest()->limit(10)->get()
            : collect();

        $payoutProfile = $code ? AffiliatePayoutProfile::where('affiliate_code_id', $code->id)->first() : null;
        $pendingPayout = $code
            ? AffiliatePayoutRequest::where('affiliate_code_id', $code->id)->where('status', 'pending')->latest()->first()
            : null;

        return view('backend.dashboards.clinic.pages.affiliate.index', [
            'code' => $code,
            'discountPercent' => $discountPercent,
            'commissionPercent' => $commissionPercent,
            'transactions' => $transactions,
            'payoutProfile' => $payoutProfile,
            'pendingPayout' => $pendingPayout,
        ]);
    }
}
