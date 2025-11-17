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
        return view('backend.dashboards.admin.pages.subscriptions.index');
    }

    public function data(Request $request)
    {
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
                return '<span>' . $days . ' days</span>';
            })
            ->addColumn('action', function ($subscription) {
                $id = $subscription->id;
                $status = $subscription->status;
                $cancelBtn = $status === 'active'
                    ? '<a href="javascript:void(0)" onclick="cancelSubscription('.$id.')" class="btn btn-sm btn-outline-warning" title="'.__('Cancel').'"><i class="fa fa-ban"></i></a>'
                    : '';
                return <<<HTML
                    <div class="d-flex gap-1">
                        <a href="javascript:void(0)" onclick="extendSubscription({$id})" class="btn btn-sm btn-outline-primary" title="Extend">
                            <i class="fa fa-calendar-plus"></i>
                        </a>
                        {$cancelBtn}
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

