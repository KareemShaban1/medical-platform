<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Services\Subscription\SubscriptionService;
use App\Services\Subscription\PlanService;
use App\Services\Subscription\SubscriptionFeatureService;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected PlanService $planService;
    protected SubscriptionFeatureService $featureService;

    public function __construct(
        SubscriptionService $subscriptionService,
        PlanService $planService,
        SubscriptionFeatureService $featureService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->planService = $planService;
        $this->featureService = $featureService;
    }

    public function index()
    {
        $user = Auth::guard('clinic')->user();
        $entity = $user->clinic_id ? $user->clinic : $user;
        $subscription = $this->subscriptionService->getEffectiveSubscription($entity);

        return view('backend.dashboards.clinic.pages.subscriptions.index', [
            'subscription' => $subscription,
            'entity' => $entity,
            'user' => $user,
        ]);
    }

    public function plans()
    {
        $user = Auth::guard('clinic')->user();
        $planType = $user->clinic_id ? 'clinic' : 'doctor';

        $plans = $this->planService->getPlansByType($planType);
        $currentSubscription = null;

        $entity = $user->clinic_id ? $user->clinic : $user;
        $currentSubscription = $this->subscriptionService->getEffectiveSubscription($entity);

        return view('backend.dashboards.clinic.pages.subscriptions.plans', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
            'planType' => $planType,
        ]);
    }

    public function subscribe(Request $request, $planId)
    {
        $user = Auth::guard('clinic')->user();
        $plan = Plan::findOrFail($planId);

        // Validate plan type matches user type
        if ($user->clinic_id && $plan->plan_type !== 'clinic') {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid plan type for clinic user')
            ], 422);
        }

        if (!$user->clinic_id && $plan->plan_type !== 'doctor') {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid plan type for standalone doctor')
            ], 422);
        }

        try {
            $entity = $user->clinic_id ? $user->clinic : $user;
            $subscription = $this->subscriptionService->subscribe($entity, $plan, [
                'status' => 'active',
                'auto_renew' => $request->boolean('auto_renew', false),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('Subscribed successfully'),
                'subscription' => $subscription->load('plan'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function cancel()
    {
        $user = Auth::guard('clinic')->user();
        $entity = $user->clinic_id ? $user->clinic : $user;
        $subscription = $this->subscriptionService->getSubscription($entity);

        if (!$subscription) {
            return response()->json([
                'status' => 'error',
                'message' => __('No subscription found')
            ], 404);
        }

        try {
            $this->subscriptionService->cancelSubscription($subscription);

            return response()->json([
                'status' => 'success',
                'message' => __('Subscription canceled successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function usage()
    {
        $user = Auth::guard('clinic')->user();
        $entity = $user->clinic_id ? $user->clinic : $user;
        $subscription = $this->subscriptionService->getEffectiveSubscription($entity);

        if (!$subscription) {
            return view('backend.dashboards.clinic.pages.subscriptions.no-subscription');
        }

        $usages = $subscription->featureUsages()->with('feature')->get();

        return view('backend.dashboards.clinic.pages.subscriptions.usage', [
            'subscription' => $subscription,
            'usages' => $usages,
        ]);
    }
}

