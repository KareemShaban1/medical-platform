<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

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
        // apply permissions
        abort_if(!hasPermission('view subscriptions'), 403, __('You are not authorized to view subscriptions'));

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
        // apply permissions
        // abort_if(!hasPermission('view subscription plans'), 403, __('You are not authorized to view subscription plans'));

        return redirect()
            ->to(route('home', ['plan_type' => 'clinic']) . '#subscriptions-plans')
            ->with('subscription_prompt', __('Please subscribe to continue.'));
    }

    public function subscribe(Request $request, $planId)
    {
        // apply permissions
        abort_if(!hasPermission('subscribe'), 403, __('You are not authorized to subscribe to plan'));

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
                'preserve_dates' => true,
            ]);

            // Notify subscribed clinic/doctor user
            if (!empty($user->email)) {
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
                    'context' => 'clinic_subscribe',
                    'subscription_id' => $subscription->id,
                    'plan_id' => $plan->id,
                    'admin_ids' => $admins->pluck('id')->all(),
                    'status' => 'stored_in_database',
                ]);
            } else {
                Log::warning('subscription.db_notification.admin_skipped', [
                    'context' => 'clinic_subscribe',
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
        // apply permissions
        abort_if(!hasPermission('cancel subscription'), 403, __('You are not authorized to cancel subscription'));

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
        // apply permissions
        abort_if(!hasPermission('view subscription usage'), 403, __('You are not authorized to view subscription usage'));

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
