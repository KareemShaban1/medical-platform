<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Subscription\PlanService;
use App\Services\Subscription\SubscriptionService;
use App\Models\Plan;
use App\Models\Admin;
use App\Models\Subscription;
use App\Notifications\SubscriptionCreatedNotification;
use App\PaymentGateways\PaymentGatewayManager;
use App\Enums\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Mail\SubscriptionCreatedUserMail;
use App\Mail\SubscriptionCreatedAdminMail;
use App\Services\Affiliate\AffiliateService;

class SubscriptionController extends Controller
{
    protected PlanService $planService;
    protected SubscriptionService $subscriptionService;
    protected PaymentGatewayManager $paymentGatewayManager;

    public function __construct(
        PlanService $planService,
        SubscriptionService $subscriptionService,
        PaymentGatewayManager $paymentGatewayManager
    ) {
        $this->planService = $planService;
        $this->subscriptionService = $subscriptionService;
        $this->paymentGatewayManager = $paymentGatewayManager;
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

        $availableGateways = $this->paymentGatewayManager->getAvailableGateways();

        return view('frontend.pages.subscriptions.plans', compact('plans', 'type', 'currentSubscription', 'availableGateways'));
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

        // For free plans, payment gateway validation is not required (will use online gateway)
        $isFreePlan = $plan->price <= 0;

        if (!$isFreePlan) {
            // Validate payment gateway for paid plans
            $request->validate([
                'payment_gateway' => 'required|string|in:'.implode(',', PaymentGateway::values()),
                'pay_method' => 'nullable|string|in:card,wallet',
                'wallet_phone' => 'nullable|string|regex:/^01[0-9]{9}$/',
            ]);

            // Additional validation: wallet phone required when paymob wallet is selected
            if ($request->payment_gateway === 'paymob' && $request->pay_method === 'wallet') {
                $request->validate([
                    'wallet_phone' => 'required|string|regex:/^01[0-9]{9}$/',
                ], [
                    'wallet_phone.required' => __('Wallet phone is required for wallet payments'),
                    'wallet_phone.regex' => __('Wallet phone must be a valid Egyptian mobile number (01XXXXXXXXX)'),
                ]);
            }
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
            DB::beginTransaction();

            $affiliateService = app(AffiliateService::class);
            $affiliateCodeValue = trim((string) $request->input('affiliate_code', ''));
            $affiliateCode = $affiliateCodeValue ? $affiliateService->resolveCode($affiliateCodeValue) : null;
            if ($affiliateCodeValue && !$affiliateCode) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => __('Invalid discount code'),
                ], 422);
            }

            // Get current active subscription to calculate upgrade difference
            $currentSubscription = $this->subscriptionService->getActiveSubscription($entity);
            $currentPlanPrice = 0;
            $isUpgrade = false;

            if ($currentSubscription && $currentSubscription->plan) {
                $currentPlanPrice = (float) $currentSubscription->plan->price;
                // Check if this is an upgrade (new plan price > current plan price)
                if ($plan->price > $currentPlanPrice) {
                    $isUpgrade = true;
                }
            }

            // Calculate payment amount: difference for upgrades, full price for new subscriptions
            $paymentAmount = $isUpgrade ? max(0, $plan->price - $currentPlanPrice) : $plan->price;

            $discountPercent = null;
            $discountAmount = null;
            $commissionPercent = null;

            if ($affiliateCode && $paymentAmount > 0) {
                $discountPercent = $affiliateService->getDiscountPercent($affiliateCode);
                $commissionPercent = $affiliateService->getCommissionPercent($affiliateCode);
                $discountAmount = $affiliateService->calculateDiscount($paymentAmount, $discountPercent);
                $paymentAmount = max(0, $paymentAmount - $discountAmount);
            }

            // Check if plan is free (price = 0) or upgrade difference is 0
            $isFreePlan = $plan->price <= 0 || $paymentAmount <= 0;

            // For free plans, use first available online gateway (paymob) but skip payment processing
            if ($isFreePlan) {
                // Get first available online gateway (excluding COD)
                $availableGateways = $this->paymentGatewayManager->getAvailableGateways();
                $onlineGateway = collect($availableGateways)->firstWhere('name', '!=', 'cod');
                $gatewayName = $onlineGateway ? $onlineGateway['name'] : 'paymob';
            } else {
                // Get payment gateway from request
                $gatewayName = $request->payment_gateway;
            }

            $gateway = $this->paymentGatewayManager->gateway($gatewayName);
            $payMethod = $request->input('pay_method', 'card');
            $walletPhone = $request->input('wallet_phone');

            if (!$gateway->isEnabled()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => __('Payment gateway is not enabled')
                ], 422);
            }

            // Generate subscription number (SUB-XXXXXX)
            $subscriptionNumber = 'SUB-' . strtoupper(uniqid());

            // For free plans, create subscription immediately (skip payment API call)
            // For paid COD, create subscription immediately
            // For paid online payment gateways, don't create subscription until payment is confirmed
            $isOnlinePayment = !$isFreePlan && $gatewayName !== 'cod';
            $paymentResponse = null;

            if ($isOnlinePayment) {
                // If paymob wallet selected, require wallet phone
                if ($gatewayName === 'paymob' && $payMethod === 'wallet' && empty($walletPhone)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => __('Wallet phone is required for wallet payments')
                    ], 422);
                }

                // Prepare payment data
                $nameParts = explode(' ', $requestingUser->name ?? 'Customer', 2);
                $firstName = $nameParts[0] ?? 'Customer';
                $lastName = $nameParts[1] ?? 'Name';

                $paymentData = [
                    'amount' => $paymentAmount, // Use calculated difference for upgrades
                    'order_id' => null,
                    'order_number' => $subscriptionNumber,
                    'currency' => 'EGP',
                    'method' => $payMethod,
                    'wallet_phone' => $walletPhone,
                    'customer' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $requestingUser->email ?? 'customer@example.com',
                        'phone' => $requestingUser->phone ?? '01000000000',
                        'city' => 'Cairo',
                        'country' => 'EG',
                        'street' => ($entity instanceof \App\Models\Clinic ? $entity->address : 'NA') ?? 'NA',
                        'building' => 'NA',
                        'apartment' => 'NA',
                        'floor' => 'NA',
                        'postal_code' => 'NA',
                        'state' => 'NA',
                    ],
                ];

                // Process payment first - if this fails, don't create subscription
                $paymentResponse = $gateway->processPayment($paymentData);

                if (!$paymentResponse->success) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => $paymentResponse->message,
                    ], 400);
                }

                // Store subscription data in session - will create subscription after payment confirmation
                session()->put('pending_subscription', [
                    'plan_id' => $plan->id,
                    'entity_type' => get_class($entity),
                    'entity_id' => $entity->id,
                    'requesting_user_id' => $requestingUser->id,
                    'requesting_user_type' => get_class($requestingUser),
                    'auto_renew' => $request->boolean('auto_renew', false),
                    'subscription_number' => $subscriptionNumber,
                    'payment_gateway' => $gatewayName,
                    'transaction_id' => $paymentResponse->transactionId,
                    'affiliate_code_id' => $affiliateCode?->id,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'commission_percent' => $commissionPercent,
                    'affiliate_amount' => $paymentAmount,
                ]);
                session()->put('payment_subscription_number', $subscriptionNumber);

                DB::commit();

                // Return response with redirect URL for payment
                return response()->json([
                    'status' => 'success',
                    'message' => __('Please complete payment to activate your subscription'),
                    'redirect_url' => $paymentResponse->redirectUrl,
                    'requires_payment' => true,
                ]);
            } else {
                // For COD or free plans, create subscription immediately
                // For free plans: use online gateway but skip payment API call
                // For COD: process payment normally
                $subscription = $this->subscriptionService->subscribe($entity, $plan, [
                    'status' => 'active',
                    'auto_renew' => $request->boolean('auto_renew', false),
                    'preserve_dates' => true,
                    'payment_method' => $isFreePlan ? 1 : 0, // Online payment for free plans, COD for paid
                    'payment_status' => 'paid',
                    'payment_gateway' => $gatewayName,
                    'transaction_id' => $isFreePlan ? 'FREE-' . $subscriptionNumber : null,
                ]);

                if ($affiliateCode && $paymentAmount > 0) {
                    $affiliateService->recordSubscriptionCommission(
                        $subscription,
                        $affiliateCode,
                        $paymentAmount,
                        $discountPercent,
                        $discountAmount,
                        $commissionPercent ?? 0
                    );
                }

                // Only process payment if plan is not free (for COD)
                if (!$isFreePlan && $gatewayName === 'cod') {
                    // Process COD payment
                    $nameParts = explode(' ', $requestingUser->name ?? 'Customer', 2);
                    $firstName = $nameParts[0] ?? 'Customer';
                    $lastName = $nameParts[1] ?? 'Name';

                    $paymentData = [
                        'amount' => $paymentAmount, // Use calculated difference for upgrades
                        'order_id' => $subscription->id,
                        'order_number' => $subscriptionNumber,
                        'currency' => 'EGP',
                        'customer' => [
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email' => $requestingUser->email ?? 'customer@example.com',
                            'phone' => $requestingUser->phone ?? '01000000000',
                            'city' => 'Cairo',
                            'country' => 'EG',
                            'street' => ($entity instanceof \App\Models\Clinic ? $entity->address : 'NA') ?? 'NA',
                            'building' => 'NA',
                            'apartment' => 'NA',
                            'floor' => 'NA',
                            'postal_code' => 'NA',
                            'state' => 'NA',
                        ],
                    ];

                    $paymentResponse = $gateway->processPayment($paymentData);

                    if (!$paymentResponse->success) {
                        throw new \Exception($paymentResponse->message);
                    }
                }
                // For free plans: skip payment API call (amount is 0, payment gateway doesn't allow 0 cost orders)

                DB::commit();

                // Send notifications (paid immediately for COD and free plans)
                if ($requestingUser && !empty($requestingUser->email)) {
                    Mail::to($requestingUser->email)->send(new SubscriptionCreatedUserMail($subscription));
                }

                // Notify admin by mail
                $adminEmail = config('mail.admin_address') ?? config('mail.from.address');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new SubscriptionCreatedAdminMail($subscription));
                }

                // Notify all active admins in database
                $admins = Admin::where('status', true)->get();
                if ($admins->count()) {
                    Notification::send($admins, new SubscriptionCreatedNotification($subscription, true));

                    Log::info('subscription.db_notification.admin', [
                        'context' => $isFreePlan ? 'frontend_subscribe_free' : 'frontend_subscribe_cod',
                        'subscription_id' => $subscription->id,
                        'plan_id' => $plan->id,
                        'admin_ids' => $admins->pluck('id')->all(),
                        'status' => 'stored_in_database',
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => __('Subscribed successfully'),
                    'subscription' => $subscription->load('plan'),
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Subscription error: ' . $e->getMessage(), [
                'plan_id' => $planId,
                'entity_type' => isset($entity) ? get_class($entity) : null,
                'entity_id' => isset($entity) ? $entity->id : null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: __('Something went wrong. Please try again.')
            ], 422);
        }
    }
}
