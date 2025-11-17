<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Supplier;
use App\Models\RentalSpace;
use App\Models\Product;
use App\Models\Clinic;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;
use App\Services\Subscription\PlanService;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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

    public function index(Request $request)
    {
        $jobs = Job::approved()->active()->get();
        $suppliers = Supplier::approved()->active()->get();
        $rentalSpaces = RentalSpace::approved()->active()->get();
        $courses = Course::active()->get();
		$products = Product::approved()->active()->get();
        $clinics = Clinic::approved()->active()->get();

        // Get subscription plans
        $planType = $request->get('plan_type', 'doctor'); // doctor, clinic, supplier
        $doctorPlans = $this->planService->getPlansByType('doctor');
        $clinicPlans = $this->planService->getPlansByType('clinic');
        $supplierPlans = $this->planService->getPlansByType('supplier');

        // Get current subscription if authenticated
        $currentSubscription = null;
        if (Auth::guard('clinic')->check()) {
            $user = Auth::guard('clinic')->user();
            if (!$user->clinic_id) {
                $currentSubscription = $this->subscriptionService->getEffectiveSubscription($user);
            } else {
                $currentSubscription = $this->subscriptionService->getEffectiveSubscription($user->clinic);
            }
        } elseif (Auth::guard('supplier')->check()) {
            $currentSubscription = $this->subscriptionService->getEffectiveSubscription(
                Auth::guard('supplier')->user()->supplier
            );
        }

        // If AJAX request, return only the plans grid
        if ($request->ajax() || $request->wantsJson()) {
            $plans = match($planType) {
                'clinic' => $clinicPlans,
                'supplier' => $supplierPlans,
                default => $doctorPlans,
            };

            return response()->json([
                'success' => true,
                'html' => view('frontend.pages.home.partials.plans-grid', compact('plans', 'planType', 'currentSubscription'))->render()
            ]);
        }

		return view('frontend.pages.home.index', compact(
            'jobs', 'suppliers', 'rentalSpaces', 'courses', 'products', 'clinics',
            'doctorPlans', 'clinicPlans', 'supplierPlans', 'planType', 'currentSubscription'
        ));
    }

    public function getGovernorates()
    {
        $governorates = Governorate::orderBy('name')->get();
        return response()->json($governorates);
    }

    public function getCities(Request $request)
    {
        $request->validate([
            'governorate_id' => 'required|exists:governorates,id'
        ]);

        $cities = City::where('governorate_id', $request->governorate_id)
            ->orderBy('name')
            ->get();

        return response()->json($cities);
    }

    public function getAreas(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id'
        ]);

        $areas = Area::where('city_id', $request->city_id)
            ->orderBy('name')
            ->get();

        return response()->json($areas);
    }
}
