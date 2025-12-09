<?php

namespace App\Traits;

use App\Services\Subscription\SubscriptionFeatureService;
use App\Exceptions\SubscriptionFeatureLimitExceeded;
use Illuminate\Support\Facades\Auth;

trait HandlesFeatureLimits
{
    /**
     * Get the authenticated entity based on guard
     */
    protected function getAuthenticatedEntity()
    {
        if (auth('clinic')->check()) {
            $user = auth('clinic')->user();
            return $user->clinic_id ? $user->clinic : $user;
        } elseif (auth('supplier')->check()) {
            return auth('supplier')->user()->supplier;
        }

        return null;
    }

    /**
     * Get the subscription plans route based on guard
     */
    protected function getSubscriptionPlansRoute(): string
    {
        // Always redirect to home page with subscriptions section anchor
        return 'home';
    }

    /**
     * Get the subscription plans URL with anchor
     */
    protected function getSubscriptionPlansUrl(): string
    {
        return route('home') . '#subscriptions-plans';
    }

    /**
     * Decide if the current request prefers a JSON response
     */
    protected function requestPrefersJson(): bool
    {
        $request = request();
        $accepts = strtolower($request->header('accept', ''));
        $contentType = strtolower($request->header('content-type', ''));

        return $request->expectsJson()
            || $request->ajax()
            || $request->wantsJson()
            || str_contains($accepts, 'application/json')
            || str_contains($contentType, 'application/json');
    }

    /**
     * Check if entity can use a feature and handle error responses
     *
     * @param mixed $entity The entity to check (Clinic, ClinicUser, Supplier)
     * @param string $featureCode The feature code to check
     * @param callable|null $onSuccess Callback to execute on success
     * @param callable|null $onIncrement Callback to execute after successful increment
     * @return mixed Response or result from onSuccess callback
     */
    protected function checkFeatureLimit(
        $entity,
        string $featureCode,
        ?callable $onSuccess = null,
        ?callable $onIncrement = null
    ) {
        $featureService = app(SubscriptionFeatureService::class);

        // Check if feature is enabled
        if (!$featureService->isFeatureEnabled($entity, $featureCode)) {
            return $this->featureNotEnabledResponse($featureCode);
        }

        // Check if user can use the feature (has quota)
        if (!$featureService->canUse($entity, $featureCode)) {
            return $this->featureLimitExceededResponse($entity, $featureCode, $featureService);
        }

        // Execute success callback if provided
        if ($onSuccess) {
            $result = $onSuccess();
        } else {
            $result = true;
        }

        // Increment usage after success
        if ($result && $onIncrement) {
            try {
                $featureService->increment($entity, $featureCode);
                $onIncrement($result);
            } catch (SubscriptionFeatureLimitExceeded $e) {
                // Handle unexpected limit exceeded after increment
                return $this->featureLimitExceededResponse($entity, $featureCode, $featureService);
            }
        } elseif ($result && !$onIncrement) {
            // Auto-increment if no callback provided
            try {
                $featureService->increment($entity, $featureCode);
            } catch (SubscriptionFeatureLimitExceeded $e) {
                // Silently handle - this shouldn't happen if check passed
            }
        }

        return $result;
    }

    /**
     * Response when feature is not enabled
     */
    protected function featureNotEnabledResponse(string $featureCode)
    {
        if ($this->requestPrefersJson()) {
            return response()->json([
                'status' => 'error',
                'message' => __('This feature requires an active subscription. Please subscribe to a plan.'),
                'error_type' => 'feature_not_enabled',
                'feature_code' => $featureCode,
                'upgrade_required' => true,
                'action_url' => $this->getSubscriptionPlansUrl(),
                'action_text' => __('View Plans'),
            ], 403);
        }

        return redirect($this->getSubscriptionPlansUrl())
            ->with('error', __('This feature requires an active subscription.'))
            ->with('upgrade_required', true)
            ->with('feature_code', $featureCode);
    }

    /**
     * Response when feature limit is exceeded
     */
    protected function featureLimitExceededResponse($entity, string $featureCode, SubscriptionFeatureService $featureService)
    {
        $usage = $featureService->getUsage($entity, $featureCode);
        $limit = $usage ? $usage->limit_count : null;
        $used = $usage ? $usage->used_count : 0;
        $remaining = $usage ? $usage->getRemainingQuota() : 0;

        $feature = $featureService->getFeatureByCode($featureCode);
        $featureName = $feature ? ($feature->name ?? $feature->code) : __('Feature');

        $message = __(':feature limit exceeded. You have used :used out of :limit. Please upgrade your plan to continue.', [
            'feature' => $featureName,
            'used' => $used,
            'limit' => $limit ?? __('unlimited'),
        ]);

        if ($this->requestPrefersJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'error_type' => 'feature_limit_exceeded',
                'feature_code' => $featureCode,
                'feature_name' => $featureName,
                'upgrade_required' => true,
                'usage' => [
                    'used' => $used,
                    'limit' => $limit,
                    'remaining' => $remaining,
                    'percentage' => $limit ? round(($used / $limit) * 100, 1) : 0,
                ],
                'action_url' => $this->getSubscriptionPlansUrl(),
                'action_text' => __('Upgrade Plan'),
            ], 403);
        }

        return redirect($this->getSubscriptionPlansUrl())
            ->with('error', $message)
            ->with('upgrade_required', true)
            ->with('feature_code', $featureCode)
            ->with('usage', [
                'used' => $used,
                'limit' => $limit,
                'remaining' => $remaining,
            ]);
    }

    /**
     * Decrement feature usage (e.g., when deleting)
     */
    protected function decrementFeatureUsage($entity, string $featureCode): void
    {
        $featureService = app(SubscriptionFeatureService::class);
        $usage = $featureService->getUsage($entity, $featureCode);

        if ($usage && $usage->used_count > 0) {
            $featureService->decrement($entity, $featureCode);
        }
    }
}
