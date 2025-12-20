<?php

namespace App\Services\Affiliate;

use App\Models\AffiliateCode;
use App\Models\AffiliateSetting;
use App\Models\AffiliateTransaction;
use App\Models\Subscription;
use Illuminate\Support\Str;

class AffiliateService
{
    public function getSettings(): AffiliateSetting
    {
        return AffiliateSetting::firstOrCreate([], [
            'default_discount_percent' => 5.00,
            'default_commission_percent' => 5.00,
        ]);
    }

    public function resolveCode(?string $code): ?AffiliateCode
    {
        if (!$code) {
            return null;
        }

        return AffiliateCode::where('code', strtoupper(trim($code)))
            ->where('is_active', true)
            ->first();
    }

    public function generateCode(string $name): string
    {
        $base = strtoupper(Str::slug($name, ''));
        if ($base === '') {
            $base = 'AFF';
        }
        $base = substr($base, 0, 8);

        $code = $base . '-' . strtoupper(Str::random(4));
        while (AffiliateCode::where('code', $code)->exists()) {
            $code = $base . '-' . strtoupper(Str::random(4));
        }

        return $code;
    }

    public function ensureCode($affiliateable): AffiliateCode
    {
        $existing = $affiliateable->affiliateCode;
        if ($existing) {
            return $existing;
        }

        $code = $this->generateCode($affiliateable->name ?? 'AFFILIATE');

        return $affiliateable->affiliateCode()->create([
            'code' => $code,
            'is_active' => true,
        ]);
    }

    public function getDiscountPercent(AffiliateCode $code): float
    {
        $settings = $this->getSettings();
        return (float) ($code->discount_percent ?? $settings->default_discount_percent ?? 0);
    }

    public function getCommissionPercent(AffiliateCode $code): float
    {
        $settings = $this->getSettings();
        return (float) ($code->commission_percent ?? $settings->default_commission_percent ?? 0);
    }

    public function calculateDiscount(float $amount, float $percent): float
    {
        if ($amount <= 0 || $percent <= 0) {
            return 0;
        }
        return round($amount * ($percent / 100), 2);
    }

    public function calculateCommission(float $amount, float $percent): float
    {
        if ($amount <= 0 || $percent <= 0) {
            return 0;
        }
        return round($amount * ($percent / 100), 2);
    }

    public function recordSubscriptionCommission(
        Subscription $subscription,
        AffiliateCode $code,
        float $amount,
        ?float $discountPercent,
        ?float $discountAmount,
        float $commissionPercent
    ): void {
        $commissionAmount = $this->calculateCommission($amount, $commissionPercent);

        AffiliateTransaction::create([
            'affiliate_code_id' => $code->id,
            'subscription_id' => $subscription->id,
            'amount' => $amount,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
        ]);

        $code->increment('balance', $commissionAmount);
        $code->increment('total_earned', $commissionAmount);

        $subscription->update([
            'affiliate_code_id' => $code->id,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
        ]);
    }
}
