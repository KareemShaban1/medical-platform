<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Services\Subscription\SubscriptionService;
use App\Services\Subscription\PlanService;
use App\Services\Subscription\SubscriptionFeatureService;
use App\Models\Plan;
use App\Models\Admin;
use App\Notifications\SubscriptionCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Mail\SubscriptionCreatedUserMail;
use App\Mail\SubscriptionCreatedAdminMail;

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
        $user = Auth::guard('supplier')->user();
        $supplier = $user->supplier;
        $subscription = $this->subscriptionService->getEffectiveSubscription($supplier);

        return view('backend.dashboards.supplier.pages.subscriptions.index', [
            'subscription' => $subscription,
            'supplier' => $supplier,
        ]);
    }

    public function plans()
    {
        $user = Auth::guard('supplier')->user();
        $supplier = $user->supplier;

        $plans = $this->planService->getPlansByType('supplier');
        $currentSubscription = $this->subscriptionService->getEffectiveSubscription($supplier);

        return view('backend.dashboards.supplier.pages.subscriptions.plans', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
        ]);
    }

    public function subscribe(Request $request, $planId)
    {
        $user = Auth::guard('supplier')->user();
        $supplier = $user->supplier;
        $plan = Plan::findOrFail($planId);

        if ($plan->plan_type !== 'supplier') {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid plan type')
            ], 422);
        }

        try {
            $subscription = $this->subscriptionService->subscribe($supplier, $plan, [
                'status' => 'active',
                'auto_renew' => $request->boolean('auto_renew', false),
                'preserve_dates' => true,
            ]);

            // Notify supplier user
            $user = Auth::guard('supplier')->user();
            if ($user && !empty($user->email)) {
                Mail::to($user->email)->send(new SubscriptionCreatedUserMail($subscription));
            }

            // Notify admin by mail
            $adminEmail = config('mail.admin_address') ?? config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new SubscriptionCreatedAdminMail($subscription));
            }

            // Store admin DB notifications
            $admins = Admin::where('status', true)->get();
            if ($admins->count()) {
                Notification::send($admins, new SubscriptionCreatedNotification($subscription, true));

                Log::info('subscription.db_notification.admin', [
                    'context' => 'supplier_subscribe',
                    'subscription_id' => $subscription->id,
                    'plan_id' => $plan->id,
                    'admin_ids' => $admins->pluck('id')->all(),
                    'status' => 'stored_in_database',
                ]);
            } else {
                Log::warning('subscription.db_notification.admin_skipped', [
                    'context' => 'supplier_subscribe',
                    'subscription_id' => $subscription->id,
                    'plan_id' => $plan->id,
                    'reason' => 'no_active_admins',
                ]);
            }

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
        $user = Auth::guard('supplier')->user();
        $supplier = $user->supplier;
        $subscription = $this->subscriptionService->getSubscription($supplier);

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
        $user = Auth::guard('supplier')->user();
        $supplier = $user->supplier;
        $subscription = $this->subscriptionService->getEffectiveSubscription($supplier);

        if (!$subscription) {
            return view('backend.dashboards.supplier.pages.subscriptions.no-subscription');
        }

        $usages = $subscription->featureUsages()->with('feature')->get();

        return view('backend.dashboards.supplier.pages.subscriptions.usage', [
            'subscription' => $subscription,
            'usages' => $usages,
        ]);
    }
}
