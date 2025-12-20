<?php

namespace App\Http\Controllers\Backend\Dashboards\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateTransaction;
use App\Services\Affiliate\AffiliateService;

class DashboardController extends Controller
{
    public function index(AffiliateService $affiliateService)
    {
        $user = auth('affiliate')->user();
        $code = $user->affiliateCode ?: $affiliateService->ensureCode($user);
        $settings = $affiliateService->getSettings();

        $discountPercent = $code?->discount_percent ?? $settings->default_discount_percent;
        $commissionPercent = $code?->commission_percent ?? $settings->default_commission_percent;

        $transactions = $code
            ? AffiliateTransaction::where('affiliate_code_id', $code->id)->latest()->limit(10)->get()
            : collect();

        return view('backend.dashboards.affiliate.pages.dashboard', [
            'affiliate' => $user,
            'code' => $code,
            'discountPercent' => $discountPercent,
            'commissionPercent' => $commissionPercent,
            'transactions' => $transactions,
        ]);
    }
}
