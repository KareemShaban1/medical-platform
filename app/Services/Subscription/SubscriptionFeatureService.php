<?php

namespace App\Services\Subscription;

use App\Models\FeatureUsage;
use App\Models\FeatureMaster;
use App\Services\Subscription\SubscriptionService;
use App\Exceptions\SubscriptionFeatureLimitExceeded;

class SubscriptionFeatureService
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Check if entity can use a feature
     */
    public function canUse($entity, string $featureCode): bool
    {
        $subscription = $this->subscriptionService->getEffectiveSubscription($entity);

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $featureUsage = FeatureUsage::where('subscription_id', $subscription->id)
            ->where('feature_code', $featureCode)
            ->first();

        if (!$featureUsage) {
            // Feature not found in subscription - check if it's enabled
            $planFeature = $subscription->plan->planFeatures()
                ->whereHas('feature', function ($q) use ($featureCode) {
                    $q->where('code', $featureCode);
                })
                ->first();

            if (!$planFeature || !$planFeature->is_enabled) {
                return false;
            }

            // Feature is enabled but no usage record - assume unlimited
            return true;
        }

        // If not limited, always allowed
        if ($featureUsage->limit_count === null) {
            return true;
        }

        // Check if quota available
        return $featureUsage->hasRemainingQuota();
    }

    /**
     * Increment feature usage
     */
    public function increment($entity, string $featureCode, int $amount = 1): void
    {
        if (!$this->canUse($entity, $featureCode)) {
            throw new SubscriptionFeatureLimitExceeded(
                "Feature limit exceeded for: {$featureCode}"
            );
        }

        $subscription = $this->subscriptionService->getEffectiveSubscription($entity);

        if (!$subscription) {
            throw new \Exception('No active subscription found');
        }

        $featureUsage = FeatureUsage::where('subscription_id', $subscription->id)
            ->where('feature_code', $featureCode)
            ->first();

        if (!$featureUsage) {
            // Create usage record if it doesn't exist
            $planFeature = $subscription->plan->planFeatures()
                ->whereHas('feature', function ($q) use ($featureCode) {
                    $q->where('code', $featureCode);
                })
                ->with('feature')
                ->first();

            if (!$planFeature) {
                throw new \Exception("Feature not found in plan: {$featureCode}");
            }

            $limit = null;
            if ($planFeature->is_limited && $planFeature->value) {
                $limit = (int) $planFeature->value;
            }

            $featureUsage = FeatureUsage::create([
                'subscription_id' => $subscription->id,
                'feature_id' => $planFeature->feature_id,
                'feature_code' => $featureCode,
                'used_count' => $amount,
                'limit_count' => $limit,
                'last_reset_at' => now(),
            ]);

            return;
        }

        // Increment usage
        $featureUsage->increment('used_count', $amount);
    }

    /**
     * Get feature usage for entity
     */
    public function getUsage($entity, string $featureCode): ?FeatureUsage
    {
        $subscription = $this->subscriptionService->getEffectiveSubscription($entity);

        if (!$subscription) {
            return null;
        }

        return FeatureUsage::where('subscription_id', $subscription->id)
            ->where('feature_code', $featureCode)
            ->first();
    }

    /**
     * Get remaining quota for feature
     */
    public function getRemainingQuota($entity, string $featureCode): ?int
    {
        $usage = $this->getUsage($entity, $featureCode);

        if (!$usage) {
            return null;
        }

        return $usage->getRemainingQuota();
    }

    /**
     * Check if feature is enabled for entity
     */
    public function isFeatureEnabled($entity, string $featureCode): bool
    {
        $subscription = $this->subscriptionService->getEffectiveSubscription($entity);

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $planFeature = $subscription->plan->planFeatures()
            ->whereHas('feature', function ($q) use ($featureCode) {
                $q->where('code', $featureCode);
            })
            ->first();

        return $planFeature && $planFeature->is_enabled;
    }

    /**
     * Get feature by code
     */
    public function getFeatureByCode(string $code): ?FeatureMaster
    {
        return FeatureMaster::where('code', $code)->first();
    }

    /**
     * Decrement feature usage
     */
    public function decrement($entity, string $featureCode, int $amount = 1): void
    {
        $subscription = $this->subscriptionService->getEffectiveSubscription($entity);

        if (!$subscription) {
            return;
        }

        $featureUsage = FeatureUsage::where('subscription_id', $subscription->id)
            ->where('feature_code', $featureCode)
            ->first();

        if ($featureUsage && $featureUsage->used_count > 0) {
            $featureUsage->decrement('used_count', $amount);
        }
    }
}

