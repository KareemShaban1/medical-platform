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
use App\PaymentGateways\PaymentGatewayManager;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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

        $availableGateways = $this->paymentGatewayManager->getAvailableGateways();

        // If AJAX request, return only the plans grid
        if ($request->ajax() || $request->wantsJson()) {
            $plans = match($planType) {
                'clinic' => $clinicPlans,
                'supplier' => $supplierPlans,
                default => $doctorPlans,
            };

            return response()->json([
                'success' => true,
                'html' => view('frontend.pages.home.partials.plans-grid', compact('plans', 'planType', 'currentSubscription', 'availableGateways'))->render()
            ]);
        }

		return view('frontend.pages.home.index', compact(
            'jobs', 'suppliers', 'rentalSpaces', 'courses', 'products', 'clinics',
            'doctorPlans', 'clinicPlans', 'supplierPlans', 'planType', 'currentSubscription', 'availableGateways'
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

    public function termsOfUse()
    {
        $lastUpdate = now()->translatedFormat('F Y');
        $sections = [
            [
                'title' => __('legal.terms.sections.platform_scope.title'),
                'body' => __('legal.terms.sections.platform_scope.body'),
                'items' => trans('legal.terms.sections.platform_scope.points'),
                'accent' => 'fa-bullseye'
            ],
            [
                'title' => __('legal.terms.sections.subscriptions.title'),
                'body' => __('legal.terms.sections.subscriptions.body'),
                'items' => trans('legal.terms.sections.subscriptions.points'),
                'accent' => 'fa-id-badge'
            ],
            [
                'title' => __('legal.terms.sections.content_creation.title'),
                'body' => __('legal.terms.sections.content_creation.body'),
                'items' => trans('legal.terms.sections.content_creation.points'),
                'accent' => 'fa-lightbulb'
            ],
            [
                'title' => __('legal.terms.sections.interaction.title'),
                'body' => __('legal.terms.sections.interaction.body'),
                'items' => trans('legal.terms.sections.interaction.points'),
                'accent' => 'fa-handshake'
            ],
            [
                'title' => __('legal.terms.sections.updates.title'),
                'body' => __('legal.terms.sections.updates.body'),
                'items' => [],
                'accent' => 'fa-arrows-rotate'
            ],
        ];

        $highlights = [
            [
                'title' => __('legal.terms.highlights.trust.title'),
                'body' => __('legal.terms.highlights.trust.body'),
                'icon' => 'fa-shield-heart'
            ],
            [
                'title' => __('legal.terms.highlights.compliance.title'),
                'body' => __('legal.terms.highlights.compliance.body'),
                'icon' => 'fa-scale-balanced'
            ],
            [
                'title' => __('legal.terms.highlights.collaboration.title'),
                'body' => __('legal.terms.highlights.collaboration.body'),
                'icon' => 'fa-people-arrows'
            ],
        ];

        return view('frontend.pages.legal.policy', [
            'hero' => [
                'title' => __('legal.terms.title'),
                'subtitle' => __('legal.terms.lede'),
            ],
            'intro' => __('legal.terms.intro'),
            'updateNotice' => __('legal.last_update', ['date' => $lastUpdate]),
            'sections' => $sections,
            'highlights' => $highlights,
        ]);
    }

    public function privacyPolicy()
    {
        $lastUpdate = now()->translatedFormat('F Y');
        $sections = [
            [
                'title' => __('legal.privacy.sections.data_collected.title'),
                'body' => __('legal.privacy.sections.data_collected.body'),
                'items' => trans('legal.privacy.sections.data_collected.points'),
                'accent' => 'fa-database'
            ],
            [
                'title' => __('legal.privacy.sections.data_use.title'),
                'body' => __('legal.privacy.sections.data_use.body'),
                'items' => trans('legal.privacy.sections.data_use.points'),
                'accent' => 'fa-rocket'
            ],
            [
                'title' => __('legal.privacy.sections.security.title'),
                'body' => __('legal.privacy.sections.security.body'),
                'items' => trans('legal.privacy.sections.security.points'),
                'accent' => 'fa-lock'
            ],
            [
                'title' => __('legal.privacy.sections.payments.title'),
                'body' => __('legal.privacy.sections.payments.body'),
                'items' => trans('legal.privacy.sections.payments.points'),
                'accent' => 'fa-credit-card'
            ],
            [
                'title' => __('legal.privacy.sections.rights.title'),
                'body' => __('legal.privacy.sections.rights.body'),
                'items' => trans('legal.privacy.sections.rights.points'),
                'accent' => 'fa-user-shield'
            ],
            [
                'title' => __('legal.privacy.sections.updates.title'),
                'body' => __('legal.privacy.sections.updates.body'),
                'items' => [],
                'accent' => 'fa-arrows-rotate'
            ],
        ];

        $highlights = [
            [
                'title' => __('legal.privacy.highlights.encryption.title'),
                'body' => __('legal.privacy.highlights.encryption.body'),
                'icon' => 'fa-shield-alt'
            ],
            [
                'title' => __('legal.privacy.highlights.choice.title'),
                'body' => __('legal.privacy.highlights.choice.body'),
                'icon' => 'fa-toggle-on'
            ],
            [
                'title' => __('legal.privacy.highlights.support.title'),
                'body' => __('legal.privacy.highlights.support.body'),
                'icon' => 'fa-headset'
            ],
        ];

        return view('frontend.pages.legal.policy', [
            'hero' => [
                'title' => __('legal.privacy.title'),
                'subtitle' => __('legal.privacy.lede'),
            ],
            'intro' => __('legal.privacy.intro'),
            'updateNotice' => __('legal.last_update', ['date' => $lastUpdate]),
            'sections' => $sections,
            'highlights' => $highlights,
        ]);
    }

    public function returnPolicy()
    {
        $lastUpdate = now()->translatedFormat('F Y');
        $sections = [
            [
                'title' => __('legal.refund.sections.finality.title'),
                'body' => __('legal.refund.sections.finality.body'),
                'items' => trans('legal.refund.sections.finality.points'),
                'accent' => 'fa-receipt'
            ],
            [
                'title' => __('legal.refund.sections.cancellations.title'),
                'body' => __('legal.refund.sections.cancellations.body'),
                'items' => trans('legal.refund.sections.cancellations.points'),
                'accent' => 'fa-calendar-xmark'
            ],
            [
                'title' => __('legal.refund.sections.errors.title'),
                'body' => __('legal.refund.sections.errors.body'),
                'items' => trans('legal.refund.sections.errors.points'),
                'accent' => 'fa-bug'
            ],
            [
                'title' => __('legal.refund.sections.support.title'),
                'body' => __('legal.refund.sections.support.body'),
                'items' => trans('legal.refund.sections.support.points'),
                'accent' => 'fa-headset'
            ],
            [
                'title' => __('legal.refund.sections.changes.title'),
                'body' => __('legal.refund.sections.changes.body'),
                'items' => [],
                'accent' => 'fa-pen-to-square'
            ],
        ];

        $highlights = [
            [
                'title' => __('legal.refund.highlights.transparency.title'),
                'body' => __('legal.refund.highlights.transparency.body'),
                'icon' => 'fa-scale-unbalanced'
            ],
            [
                'title' => __('legal.refund.highlights.service.title'),
                'body' => __('legal.refund.highlights.service.body'),
                'icon' => 'fa-screwdriver-wrench'
            ],
            [
                'title' => __('legal.refund.highlights.communication.title'),
                'body' => __('legal.refund.highlights.communication.body'),
                'icon' => 'fa-comment-dots'
            ],
        ];

        return view('frontend.pages.legal.policy', [
            'hero' => [
                'title' => __('legal.refund.title'),
                'subtitle' => __('legal.refund.lede'),
            ],
            'intro' => __('legal.refund.intro'),
            'updateNotice' => __('legal.last_update', ['date' => $lastUpdate]),
            'sections' => $sections,
            'highlights' => $highlights,
        ]);
    }

    public function shippingPolicy()
    {
        $lastUpdate = now()->translatedFormat('F Y');
        $sections = [
            [
                'title' => __('legal.shipping.sections.coverage.title'),
                'body' => __('legal.shipping.sections.coverage.body'),
                'items' => trans('legal.shipping.sections.coverage.points'),
                'accent' => 'fa-location-dot'
            ],
            [
                'title' => __('legal.shipping.sections.processing.title'),
                'body' => __('legal.shipping.sections.processing.body'),
                'items' => trans('legal.shipping.sections.processing.points'),
                'accent' => 'fa-clock'
            ],
            [
                'title' => __('legal.shipping.sections.delivery.title'),
                'body' => __('legal.shipping.sections.delivery.body'),
                'items' => trans('legal.shipping.sections.delivery.points'),
                'accent' => 'fa-truck'
            ],
            [
                'title' => __('legal.shipping.sections.fees.title'),
                'body' => __('legal.shipping.sections.fees.body'),
                'items' => trans('legal.shipping.sections.fees.points'),
                'accent' => 'fa-file-invoice-dollar'
            ],
            [
                'title' => __('legal.shipping.sections.receiving.title'),
                'body' => __('legal.shipping.sections.receiving.body'),
                'items' => trans('legal.shipping.sections.receiving.points'),
                'accent' => 'fa-box-open'
            ],
        ];

        $highlights = [
            [
                'title' => __('legal.shipping.highlights.speed.title'),
                'body' => __('legal.shipping.highlights.speed.body'),
                'icon' => 'fa-bolt'
            ],
            [
                'title' => __('legal.shipping.highlights.visibility.title'),
                'body' => __('legal.shipping.highlights.visibility.body'),
                'icon' => 'fa-eye'
            ],
            [
                'title' => __('legal.shipping.highlights.care.title'),
                'body' => __('legal.shipping.highlights.care.body'),
                'icon' => 'fa-hand-holding-heart'
            ],
        ];

        return view('frontend.pages.legal.policy', [
            'hero' => [
                'title' => __('legal.shipping.title'),
                'subtitle' => __('legal.shipping.lede'),
            ],
            'intro' => __('legal.shipping.intro'),
            'updateNotice' => __('legal.last_update', ['date' => $lastUpdate]),
            'sections' => $sections,
            'highlights' => $highlights,
        ]);
    }

    public function aboutUs()
    {
        return view('frontend.pages.legal.about', [
            'hero' => [
                'title' => __('legal.about.title'),
                'subtitle' => __('legal.about.lede'),
            ],
            'pillars' => [
                [
                    'title' => __('legal.about.pillars.network.title'),
                    'body' => __('legal.about.pillars.network.body'),
                    'icon' => 'fa-stethoscope'
                ],
                [
                    'title' => __('legal.about.pillars.procurement.title'),
                    'body' => __('legal.about.pillars.procurement.body'),
                    'icon' => 'fa-cart-flatbed'
                ],
                [
                    'title' => __('legal.about.pillars.learning.title'),
                    'body' => __('legal.about.pillars.learning.body'),
                    'icon' => 'fa-graduation-cap'
                ],
            ],
            'milestones' => [
                [
                    'title' => __('legal.about.milestones.launch.title'),
                    'body' => __('legal.about.milestones.launch.body'),
                    'year' => __('legal.about.milestones.launch.year'),
                ],
                [
                    'title' => __('legal.about.milestones.network.title'),
                    'body' => __('legal.about.milestones.network.body'),
                    'year' => __('legal.about.milestones.network.year'),
                ],
                [
                    'title' => __('legal.about.milestones.vision.title'),
                    'body' => __('legal.about.milestones.vision.body'),
                    'year' => __('legal.about.milestones.vision.year'),
                ],
            ],
        ]);
    }
}
