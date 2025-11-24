<?php

namespace App\Http\Controllers\Frontend\Doctor;

use App\Http\Controllers\Controller;
use App\Services\Subscription\SubscriptionService;
use App\Services\Subscription\SubscriptionFeatureService;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected SubscriptionFeatureService $featureService;

    public function __construct(
        SubscriptionService $subscriptionService,
        SubscriptionFeatureService $featureService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->featureService = $featureService;
    }

    public function index()
    {
        $doctor = Auth::guard('clinic')->user();

        if (!$doctor || $doctor->clinic_id) {
            return redirect()->route('home');
        }

        $subscription = $this->subscriptionService->getEffectiveSubscription($doctor);

        return view('frontend.doctor.subscription', compact('doctor', 'subscription'));
    }

    public function cancel()
    {
        $doctor = Auth::guard('clinic')->user();

        if (!$doctor || $doctor->clinic_id) {
            return response()->json([
                'status' => 'error',
                'message' => __('Unauthorized')
            ], 403);
        }

        $subscription = $this->subscriptionService->getSubscription($doctor);

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
}

