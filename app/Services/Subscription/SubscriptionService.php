<?php

namespace App\Services\Subscription;

use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Clinic;
use App\Models\ClinicUser;
use App\Models\Supplier;
use App\Models\FeatureUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Get or create subscription for an entity
     */
    public function getSubscription($entity): ?Subscription
    {
        return $entity->subscription;
    }

    /**
     * Get effective subscription (for clinic users, check clinic subscription first)
     */
    public function getEffectiveSubscription($entity): ?Subscription
    {
        // If ClinicUser with clinic_id, check clinic subscription
        if ($entity instanceof ClinicUser && $entity->clinic_id) {
            $clinicSubscription = $entity->clinic?->subscription;
            if ($clinicSubscription && $clinicSubscription->isActive()) {
                return $clinicSubscription;
            }
        }

        // If SupplierUser, check supplier subscription
        if ($entity instanceof \App\Models\SupplierUser) {
            $supplierSubscription = $entity->supplier?->subscription;
            if ($supplierSubscription && $supplierSubscription->isActive()) {
                return $supplierSubscription;
            }
        }

        // Return entity's own subscription
        return $entity->subscription;
    }

    /**
     * Subscribe entity to a plan
     */
    public function subscribe($entity, Plan $plan, array $options = []): Subscription
    {
        return DB::transaction(function () use ($entity, $plan, $options) {
            // Cancel existing active subscription if exists
            $existing = $this->getSubscription($entity);
            if ($existing && $existing->isActive()) {
                $existing->update(['status' => 'canceled']);
            }

            $startDate = $options['start_date'] ?? now();
            $endDate = null;

            if ($plan->duration_in_days) {
                $endDate = Carbon::parse($startDate)->addDays($plan->duration_in_days);
            }

            $subscription = Subscription::create([
                'subscribable_type' => get_class($entity),
                'subscribable_id' => $entity->id,
                'plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $options['status'] ?? 'active',
                'auto_renew' => $options['auto_renew'] ?? false,
            ]);

            // Initialize feature usages
            $this->initializeFeatureUsages($subscription, $plan);

            return $subscription;
        });
    }

    /**
     * Initialize feature usages for a subscription
     */
    public function initializeFeatureUsages(Subscription $subscription, Plan $plan): void
    {
        $planFeatures = $plan->planFeatures()->with('feature')->get();

        foreach ($planFeatures as $planFeature) {
            if (!$planFeature->is_enabled) {
                continue;
            }

            $limit = null;
            if ($planFeature->is_limited && $planFeature->value) {
                $limit = (int) $planFeature->value;
            }

            FeatureUsage::create([
                'subscription_id' => $subscription->id,
                'feature_id' => $planFeature->feature_id,
                'feature_code' => $planFeature->feature->code,
                'used_count' => 0,
                'limit_count' => $limit,
                'last_reset_at' => now(),
            ]);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        return $subscription->update([
            'status' => 'canceled',
        ]);
    }

    /**
     * Renew subscription
     */
    public function renewSubscription(Subscription $subscription): Subscription
    {
        $plan = $subscription->plan;

        if (!$plan->duration_in_days) {
            throw new \Exception('Cannot renew subscription without duration');
        }

        $newEndDate = $subscription->end_date
            ? Carbon::parse($subscription->end_date)->addDays($plan->duration_in_days)
            : now()->addDays($plan->duration_in_days);

        $subscription->update([
            'end_date' => $newEndDate,
            'status' => 'active',
        ]);

        // Reset feature usages if needed
        $this->resetFeatureUsages($subscription);

        return $subscription;
    }

    /**
     * Reset feature usages for subscription
     */
    public function resetFeatureUsages(Subscription $subscription): void
    {
        $subscription->featureUsages()->update([
            'used_count' => 0,
            'last_reset_at' => now(),
        ]);
    }

    /**
     * Check if entity has active subscription
     */
    public function hasActiveSubscription($entity): bool
    {
        $subscription = $this->getEffectiveSubscription($entity);
        return $subscription && $subscription->isActive();
    }

    /**
     * Get subscription status for entity
     */
    public function getSubscriptionStatus($entity): string
    {
        $subscription = $this->getEffectiveSubscription($entity);

        if (!$subscription) {
            return 'none';
        }

        if ($subscription->isActive()) {
            return 'active';
        }

        if ($subscription->isExpired()) {
            return 'expired';
        }

        return $subscription->status;
    }
}

