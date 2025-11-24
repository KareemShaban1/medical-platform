<?php

namespace App\Services\Subscription;

use App\Models\Plan;
use App\Models\FeatureMaster;
use App\Models\PlanFeature;
use Illuminate\Support\Facades\DB;

class PlanService
{
    /**
     * Get plans by type
     */
    public function getPlansByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return Plan::active()
            ->forType($type)
            ->with('planFeatures.feature')
            ->orderBy('price')
            ->get();
    }

    /**
     * Get plan by ID
     */
    public function getPlan(int $id): ?Plan
    {
        return Plan::with('planFeatures.feature')->find($id);
    }

    /**
     * Create plan with features
     */
    public function createPlan(array $planData, array $features = []): Plan
    {
        return DB::transaction(function () use ($planData, $features) {
            $plan = Plan::create($planData);

            foreach ($features as $feature) {
                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'feature_id' => $feature['feature_id'],
                    'is_enabled' => $feature['is_enabled'] ?? true,
                    'value' => $feature['value'] ?? null,
                    'is_limited' => $feature['is_limited'] ?? false,
                ]);
            }

            return $plan->load('planFeatures.feature');
        });
    }

    /**
     * Update plan
     */
    public function updatePlan(Plan $plan, array $planData, array $features = []): Plan
    {
        return DB::transaction(function () use ($plan, $planData, $features) {
            $plan->update($planData);

            // Update features if provided
            if (!empty($features)) {
                // Delete existing features
                $plan->planFeatures()->delete();

                // Create new features
                foreach ($features as $feature) {
                    PlanFeature::create([
                        'plan_id' => $plan->id,
                        'feature_id' => $feature['feature_id'],
                        'is_enabled' => $feature['is_enabled'] ?? true,
                        'value' => $feature['value'] ?? null,
                        'is_limited' => $feature['is_limited'] ?? false,
                    ]);
                }
            }

            return $plan->fresh()->load('planFeatures.feature');
        });
    }

    /**
     * Delete plan
     */
    public function deletePlan(Plan $plan): bool
    {
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            throw new \Exception('Cannot delete plan with active subscriptions');
        }

        return $plan->delete();
    }

    /**
     * Get all features
     */
    public function getAllFeatures(): \Illuminate\Database\Eloquent\Collection
    {
        return FeatureMaster::where('is_active', true)->get();
    }

    /**
     * Get feature by code
     */
    public function getFeatureByCode(string $code): ?FeatureMaster
    {
        return FeatureMaster::where('code', $code)->first();
    }
}

