<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Subscription\SubscriptionFeatureService;
use App\Exceptions\SubscriptionFeatureLimitExceeded;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    protected SubscriptionFeatureService $featureService;

    public function __construct(SubscriptionFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $featureCode
     */
    public function handle(Request $request, Closure $next, string $featureCode): Response
    {
        $user = null;

        $entity = null;
        $redirectRoute = null;
        $wantsJson = $this->wantsJson($request);

        if (auth('clinic')->check()) {
            $user = auth('clinic')->user();
            $entity = $user->clinic_id ? $user->clinic : $user;
            $redirectRoute = 'clinic.subscriptions.plans';
        } elseif (auth('supplier')->check()) {
            $entity = auth('supplier')->user()->supplier;
            $redirectRoute = 'supplier.subscriptions.plans';
        } else {
            return $next($request);
        }

        // Check if feature is enabled
        if (!$this->featureService->isFeatureEnabled($entity, $featureCode)) {
            if ($wantsJson) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('This feature requires a subscription'),
                    'error_type' => 'feature_not_enabled',
                    'feature_code' => $featureCode,
                    'upgrade_required' => true,
                ], 403);
            }

            return redirect()->route($redirectRoute)
                ->with('error', __('This feature requires a subscription'))
                ->with('upgrade_required', true)
                ->with('feature_code', $featureCode);
        }

        // Check if user can use the feature (has quota)
        if (!$this->featureService->canUse($entity, $featureCode)) {
            $usage = $this->featureService->getUsage($entity, $featureCode);

            if ($wantsJson) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Feature limit exceeded. Please upgrade your plan.'),
                    'error_type' => 'feature_limit_exceeded',
                    'feature_code' => $featureCode,
                    'upgrade_required' => true,
                    'usage' => [
                        'used' => $usage ? $usage->used_count : 0,
                        'limit' => $usage ? $usage->limit_count : null,
                        'remaining' => $usage ? $usage->getRemainingQuota() : null,
                    ]
                ], 403);
            }

            return redirect()->route($redirectRoute)
                ->with('error', __('Feature limit exceeded. Please upgrade your plan.'))
                ->with('upgrade_required', true)
                ->with('feature_code', $featureCode);
        }

        return $next($request);
    }

    /**
     * Determine if request should receive JSON response
     */
    protected function wantsJson(Request $request): bool
    {
        $accepts = strtolower($request->header('accept', ''));
        $contentType = strtolower($request->header('content-type', ''));

        return $request->expectsJson()
            || $request->ajax()
            || $request->wantsJson()
            || str_contains($accepts, 'application/json')
            || str_contains($contentType, 'application/json');
    }
}
