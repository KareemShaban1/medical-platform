<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Subscription\PlanService;
use App\Services\Subscription\SubscriptionService;
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
    protected PlanService $planService;
    protected SubscriptionService $subscriptionService;

    public function __construct(
        PlanService $planService,
        SubscriptionService $subscriptionService
    ) {
        $this->planService = $planService;
        $this->subscriptionService = $subscriptionService;
    }

    public function plans(Request $request)
    {
        $type = $request->get('type', 'doctor'); // doctor, clinic, supplier
        $plans = $this->planService->getPlansByType($type);

        // Get current subscription if authenticated
        $currentSubscription = null;
        if ($type === 'doctor' && Auth::guard('clinic')->check()) {
            $user = Auth::guard('clinic')->user();
            if (!$user->clinic_id) {
                $currentSubscription = $this->subscriptionService->getEffectiveSubscription($user);
            }
        } elseif ($type === 'clinic' && Auth::guard('clinic')->check()) {
            $user = Auth::guard('clinic')->user();
            if ($user->clinic_id) {
                $currentSubscription = $this->subscriptionService->getEffectiveSubscription($user->clinic);
            }
        } elseif ($type === 'supplier' && Auth::guard('supplier')->check()) {
            $currentSubscription = $this->subscriptionService->getEffectiveSubscription(
                Auth::guard('supplier')->user()->supplier
            );
        }

        return view('frontend.pages.subscriptions.plans', compact('plans', 'type', 'currentSubscription'));
    }

    public function subscribe(Request $request, $planId)
    {
        // Always return JSON for AJAX requests
        if (!$request->expectsJson() && !$request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid request format')
            ], 400);
        }

        try {
            $plan = Plan::findOrFail($planId);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Plan not found')
            ], 404);
        }

        // Determine entity based on plan type and auth
        $entity = null;
        $requestingUser = null;

        if ($plan->plan_type === 'doctor') {
            if (!Auth::guard('clinic')->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Please login as a doctor to subscribe')
                ], 401);
            }
            $user = Auth::guard('clinic')->user();
            if ($user->clinic_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('This plan is for standalone doctors only')
                ], 422);
            }
            $entity = $user;
            $requestingUser = $user;
        } elseif ($plan->plan_type === 'clinic') {
            if (!Auth::guard('clinic')->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Please login as a clinic user to subscribe')
                ], 401);
            }
            $user = Auth::guard('clinic')->user();
            if (!$user->clinic_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('This plan is for clinics only')
                ], 422);
            }
            $entity = $user->clinic;
            $requestingUser = $user;
        } elseif ($plan->plan_type === 'supplier') {
            if (!Auth::guard('supplier')->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Please login as a supplier to subscribe')
                ], 401);
            }
            $requestingUser = Auth::guard('supplier')->user();
            $entity = $requestingUser->supplier;
        }

        if (!$entity) {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid subscription request')
            ], 422);
        }

        try {
            $subscription = $this->subscriptionService->subscribe($entity, $plan, [
                'status' => 'active',
                'auto_renew' => $request->boolean('auto_renew', false),
                'preserve_dates' => true,
            ]);

            // Notify subscribed user
            if ($requestingUser && !empty($requestingUser->email)) {
                Mail::to($requestingUser->email)->send(new SubscriptionCreatedUserMail($subscription));
            }

            // Notify admin by mail
            $adminEmail = config('mail.admin_address') ?? config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new SubscriptionCreatedAdminMail($subscription));
            }

            // Notify all active admins in database (Laravel notifications)
            $admins = Admin::where('status', true)->get();
            if ($admins->count()) {
                Notification::send($admins, new SubscriptionCreatedNotification($subscription, true));

                Log::info('subscription.db_notification.admin', [
                    'context' => 'frontend_subscribe',
                    'subscription_id' => $subscription->id,
                    'plan_id' => $plan->id,
                    'admin_ids' => $admins->pluck('id')->all(),
                    'status' => 'stored_in_database',
                ]);
            } else {
                Log::warning('subscription.db_notification.admin_skipped', [
                    'context' => 'frontend_subscribe',
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
            \Log::error('Subscription error: ' . $e->getMessage(), [
                'plan_id' => $planId,
                'entity_type' => get_class($entity),
                'entity_id' => $entity->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: __('Something went wrong. Please try again.')
            ], 422);
        }
    }
}
