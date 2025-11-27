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
     * Plan level weights for comparison (higher is better)
     */
    protected array $levelWeights = [
        'free' => 1,
        'basic' => 2,
        'advanced' => 3,
        'vip' => 4,
    ];

    /**
     * Get or create subscription for an entity
     */
    public function getSubscription($entity): ?Subscription
    {
        return Subscription::where('subscribable_type', get_class($entity))
            ->where('subscribable_id', $entity->id)
            ->latest('created_at')
            ->first();
    }

    /**
     * Get effective subscription (for clinic users, check clinic subscription first)
     */
    public function getEffectiveSubscription($entity): ?Subscription
    {
        // If ClinicUser with clinic_id, check clinic subscription
        if ($entity instanceof ClinicUser && $entity->clinic_id) {
            $clinic = $entity->clinic;
            if ($clinic) {
                $clinicActive = $this->getActiveSubscription($clinic);
                if ($clinicActive) {
                    return $clinicActive;
                }
            }
        }

        // If SupplierUser, check supplier subscription
        if ($entity instanceof \App\Models\SupplierUser) {
            $supplier = $entity->supplier;
            if ($supplier) {
                $supplierActive = $this->getActiveSubscription($supplier);
                if ($supplierActive) {
                    return $supplierActive;
                }
            }
        }

        // Prefer entity's own active subscription
        $active = $this->getActiveSubscription($entity);
        if ($active) {
            return $active;
        }

        // Fall back to the most recent subscription (even if canceled/expired)
        return $this->getSubscription($entity);
    }

    /**
     * Get active subscription for an entity (if any)
     */
    public function getActiveSubscription($entity): ?Subscription
    {
        return Subscription::where('subscribable_type', get_class($entity))
            ->where('subscribable_id', $entity->id)
            ->active()
            ->latest('created_at')
            ->first();
    }

    /**
     * Get numeric rank for plan level
     */
    protected function getLevelRank(?string $level): ?int
    {
        return $level !== null && isset($this->levelWeights[$level])
            ? $this->levelWeights[$level]
            : null;
    }

    /**
     * Subscribe entity to a plan
     */
    public function subscribe($entity, Plan $plan, array $options = []): Subscription
    {
        return DB::transaction(function () use ($entity, $plan, $options) {
            $preserveDates = $options['preserve_dates'] ?? false;

            // Check downgrade rules against existing active subscription
            $allowDowngrade = $options['allow_downgrade'] ?? false;
            $existingActive = $this->getActiveSubscription($entity);
            $currentRank = null;
            $newRank = $this->getLevelRank($plan->level ?? null);
            $isUpgrade = false;

            if ($existingActive && !$allowDowngrade) {
                $currentPlan = $existingActive->plan;
                if ($currentPlan) {
                    $currentRank = $this->getLevelRank($currentPlan->level ?? null);

                    if ($currentRank !== null && $newRank !== null && $newRank < $currentRank) {
                        throw new \Exception(__('You cannot subscribe to a lower plan while you have an active subscription.'));
                    }

                    if ($currentRank !== null && $newRank !== null && $newRank > $currentRank) {
                        $isUpgrade = true;
                    }
                }
            }

            // Cancel existing active subscription if exists (enforce single active)
            if ($existingActive) {
                $existingActive->update(['status' => 'canceled']);
            }

            // Determine start and end dates
            if ($isUpgrade && $preserveDates && $existingActive) {
                // For upgrades, keep the original subscription period
                $startDate = $existingActive->start_date ?? now();
                $endDate = $existingActive->end_date;
            } else {
                $startDate = $options['start_date'] ?? now();

                if (array_key_exists('end_date', $options) && $options['end_date'] !== null) {
                    $endDate = Carbon::parse($options['end_date']);
                } else {
                    $endDate = null;
                    if ($plan->duration_in_days) {
                        $endDate = Carbon::parse($startDate)->addDays($plan->duration_in_days);
                    }
                }
            }

            $subscription = Subscription::create([
                'subscribable_type' => get_class($entity),
                'subscribable_id' => $entity->id,
                'plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $options['status'] ?? 'active',
                'auto_renew' => $options['auto_renew'] ?? false,
                'payment_method' => $options['payment_method'] ?? 0,
                'payment_status' => $options['payment_status'] ?? 'pending',
                'payment_gateway' => $options['payment_gateway'] ?? null,
                'transaction_id' => $options['transaction_id'] ?? null,
            ]);

            // Initialize feature usages
            $this->initializeFeatureUsages($subscription, $plan);

            // If this was an upgrade, carry over usage for common features
            if ($isUpgrade && $existingActive) {
                $oldUsages = $existingActive->featureUsages()->get()->keyBy('feature_id');
                $newUsages = $subscription->featureUsages()->get()->keyBy('feature_id');

                foreach ($newUsages as $featureId => $newUsage) {
                    if (isset($oldUsages[$featureId])) {
                        $oldUsage = $oldUsages[$featureId];
                        $newUsage->update([
                            'used_count' => $oldUsage->used_count,
                            'last_reset_at' => $oldUsage->last_reset_at,
                        ]);
                    }
                }
            }

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
        return (bool) $this->getActiveSubscription($entity);
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
