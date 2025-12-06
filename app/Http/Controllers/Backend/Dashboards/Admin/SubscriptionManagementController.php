<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Services\Subscription\SubscriptionService;
use App\Services\Subscription\PlanService;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Clinic;
use App\Models\ClinicUser;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionManagementController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected PlanService $planService;

    public function __construct(
        SubscriptionService $subscriptionService,
        PlanService $planService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->planService = $planService;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view subscriptions'), 403, __('You are not authorized to view subscriptions'));
        return view('backend.dashboards.admin.pages.subscriptions.index');
    }

    public function analytics(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view subscriptions'), 403, __('You are not authorized to view subscriptions'));
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $subscriptions = Subscription::with(['plan', 'subscribable'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        $totalCount = $subscriptions->count();
        $activeCount = $subscriptions->where('status', 'active')->count();
        $expiredCount = $subscriptions->where('status', 'expired')->count();
        $pendingCount = $subscriptions->where('status', 'pending')->count();
        $canceledCount = $subscriptions->where('status', 'canceled')->count();

        $totalRevenue = (float) $subscriptions->sum(function ($sub) {
            return $sub->plan?->price ?? 0;
        });

        $averageRevenue = $totalCount > 0 ? $totalRevenue / $totalCount : 0;

        $subscriptionsByDate = $subscriptions->groupBy(function ($sub) {
            return $sub->created_at->format('Y-m-d');
        })->map->count();

        $revenueByDate = $subscriptions->groupBy(function ($sub) {
            return $sub->created_at->format('Y-m-d');
        })->map(function ($group) {
            return (float) $group->sum(function ($sub) {
                return $sub->plan?->price ?? 0;
            });
        });

        $subscriptionsByPlanType = $subscriptions->groupBy(function ($sub) {
            return $sub->plan?->plan_type ?? __('Unknown');
        })->map->count()->sortDesc();

        $subscriptionsByStatus = [
            'active' => $activeCount,
            'expired' => $expiredCount,
            'pending' => $pendingCount,
            'canceled' => $canceledCount,
        ];

        $topPlans = $subscriptions->groupBy(function ($sub) {
            return $sub->plan?->name ?? __('Unknown');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'revenue' => (float) $group->sum(function ($sub) {
                    return $sub->plan?->price ?? 0;
                }),
                'plan_type' => $group->first()->plan?->plan_type ?? null,
                'level' => $group->first()->plan?->level ?? null,
            ];
        })->sortByDesc('count')->take(5);

        $topEntities = $subscriptions->groupBy(function ($sub) {
            $type = class_basename($sub->subscribable_type);
            $name = $sub->subscribable->name ?? '-';
            return $name . ' (' . $type . ')';
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'plan_type' => $group->first()->plan?->plan_type ?? null,
            ];
        })->sortByDesc('count')->take(5);

        $recentSubscriptions = $subscriptions->sortByDesc('created_at')->take(10);

        $analytics = [
            'total_count' => $totalCount,
            'total_revenue' => $totalRevenue,
            'average_revenue' => $averageRevenue,
            'status_counts' => $subscriptionsByStatus,
            'subscriptions_by_date' => $subscriptionsByDate,
            'revenue_by_date' => $revenueByDate,
            'subscriptions_by_plan_type' => $subscriptionsByPlanType,
            'top_plans' => $topPlans,
            'top_entities' => $topEntities,
            'recent_subscriptions' => $recentSubscriptions,
        ];

        return view('backend.dashboards.admin.pages.subscriptions.analytics', compact(
            'analytics',
            'startDate',
            'endDate'
        ));
    }

    public function data(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view subscriptions'), 403, __('You are not authorized to view subscriptions'));
        $query = Subscription::with(['plan', 'subscribable']);

        // Filters
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_type') && $request->plan_type !== 'all') {
            $query->whereHas('plan', function($q) use ($request) {
                $q->where('plan_type', $request->plan_type);
            });
        }

        if ($request->filled('entity_type') && $request->entity_type !== 'all') {
            $query->where('subscribable_type', $request->entity_type);
        }

        return DataTables::of($query)
            ->addColumn('entity_name', function ($subscription) {
                if ($subscription->subscribable_type === 'App\Models\Clinic') {
                    return $subscription->subscribable->name ?? '-';
                } elseif ($subscription->subscribable_type === 'App\Models\ClinicUser') {
                    return $subscription->subscribable->name . ($subscription->subscribable->clinic_id ? ' (Clinic User)' : ' (Standalone Doctor)');
                } elseif ($subscription->subscribable_type === 'App\Models\Supplier') {
                    return $subscription->subscribable->name ?? '-';
                }
                return '-';
            })
            ->addColumn('entity_type', function ($subscription) {
                $type = class_basename($subscription->subscribable_type);
                if ($type === 'Clinic') {
                    return 'Clinic';
                } elseif ($type === 'ClinicUser') {
                    return $subscription->subscribable->clinic_id ? 'Clinic User' : 'Standalone Doctor';
                } elseif ($type === 'Supplier') {
                    return 'Supplier';
                }
                return $type;
            })
            ->addColumn('plan_name', fn($sub) => $sub->plan->name ?? '-')
            ->addColumn('plan_type', fn($sub) => $sub->plan->plan_type ?? '-')
            ->editColumn('status', function ($subscription) {
                $statusClass = 'bg-secondary';
                if ($subscription->status === 'active') {
                    $statusClass = 'bg-success';
                } elseif ($subscription->status === 'expired') {
                    $statusClass = 'bg-danger';
                } elseif ($subscription->status === 'pending') {
                    $statusClass = 'bg-warning';
                } elseif ($subscription->status === 'canceled') {
                    $statusClass = 'bg-secondary';
                }
                return '<span class="badge ' . $statusClass . '">' . ucfirst($subscription->status) . '</span>';
            })
            ->editColumn('start_date', fn($sub) => $sub->start_date?->format('M d, Y') ?? '-')
            ->editColumn('end_date', fn($sub) => $sub->end_date?->format('M d, Y') ?? 'Lifetime')
            ->addColumn('days_remaining', function ($subscription) {
                if (!$subscription->end_date) {
                    return '<span class="text-success">Lifetime</span>';
                }
                $days = now()->diffInDays($subscription->end_date, false);
                if ($days < 0) {
                    return '<span class="text-danger">Expired</span>';
                }
                return '<span>' . number_format($days, 2) . ' days</span>';
            })
            ->addColumn('action', function ($subscription) {
                $id = $subscription->id;
                return <<<HTML
                    <div class="d-flex gap-1">
                        <a href="javascript:void(0)" onclick="extendSubscription({$id})" class="btn btn-sm btn-outline-primary" title="Extend">
                            <i class="fa fa-calendar-plus"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="deleteSubscription({$id})" class="btn btn-sm btn-outline-danger" title="Delete">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                HTML;
            })
            ->rawColumns(['status', 'days_remaining', 'action'])
            ->make(true);
    }

    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create subscription'), 403, __('You are not authorized to create subscription'));
        $plans = Plan::active()->get();
        return response()->json([
            'success' => true,
            'html' => view('backend.dashboards.admin.pages.subscriptions.create', compact('plans'))->render()
        ]);
    }

    public function getEntities(Request $request)
    {
        $type = $request->get('type');
        $entities = [];

        if ($type === 'clinic') {
            $entities = Clinic::select('id', 'name')->get()->map(function($clinic) {
                return ['id' => $clinic->id, 'name' => $clinic->name];
            });
        } elseif ($type === 'doctor') {
            $entities = ClinicUser::whereNull('clinic_id')
                ->select('id', 'name')
                ->get()
                ->map(function($doctor) {
                    return ['id' => $doctor->id, 'name' => $doctor->name];
                });
        } elseif ($type === 'supplier') {
            $entities = Supplier::select('id', 'name')->get()->map(function($supplier) {
                return ['id' => $supplier->id, 'name' => $supplier->name];
            });
        }

        return response()->json($entities);
    }

    public function store(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('create subscription'), 403, __('You are not authorized to create subscription'));
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'entity_type' => 'required|in:clinic,doctor,supplier',
            'entity_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,expired,pending,canceled',
            'auto_renew' => 'boolean',
        ]);

        try {
            $plan = Plan::findOrFail($validated['plan_id']);

            // Resolve entity
            $entity = null;
            $entityClass = null;

            if ($validated['entity_type'] === 'clinic') {
                $entity = Clinic::findOrFail($validated['entity_id']);
                $entityClass = Clinic::class;
            } elseif ($validated['entity_type'] === 'doctor') {
                $entity = ClinicUser::whereNull('clinic_id')->findOrFail($validated['entity_id']);
                $entityClass = ClinicUser::class;
            } elseif ($validated['entity_type'] === 'supplier') {
                $entity = Supplier::findOrFail($validated['entity_id']);
                $entityClass = Supplier::class;
            }

            // Validate plan type matches entity type
            if ($plan->plan_type !== $validated['entity_type']) {
                throw new \Exception(__('Plan type does not match entity type'));
            }

            $subscription = $this->subscriptionService->subscribe($entity, $plan, [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'status' => $validated['status'],
                'auto_renew' => $validated['auto_renew'] ?? false,
                'allow_downgrade' => true,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('Subscription created successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function extend(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('extend subscription'), 403, __('You are not authorized to extend subscription'));
        $validated = $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        try {
            $subscription = Subscription::findOrFail($id);
            $currentEndDate = $subscription->end_date ?? now();
            $newEndDate = \Carbon\Carbon::parse($currentEndDate)->addDays($validated['days']);

            $subscription->update([
                'end_date' => $newEndDate,
                'status' => 'active',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('Subscription extended successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function cancel($id)
    {
        // apply permissions
        abort_if(!hasPermission('cancel subscription'), 403, __('You are not authorized to cancel subscription'));
        try {
            $subscription = Subscription::findOrFail($id);
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

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete subscription'), 403, __('You are not authorized to delete subscription'));
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();

            return response()->json([
                'status' => 'success',
                'message' => __('Subscription deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}