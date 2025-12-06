<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Services\Subscription\PlanService;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Facades\DataTables;

class PlanController extends Controller
{
    protected PlanService $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view plans'), 403, __('You are not authorized to view plans'));
        return view('backend.dashboards.admin.pages.plans.index');
    }

    public function data(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view plans'), 403, __('You are not authorized to view plans'));
        $query = Plan::with('planFeatures.feature');

        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        return DataTables::of($query)
            ->addColumn('features_count', fn($plan) => $plan->planFeatures->count())
            ->addColumn('active_subscriptions', fn($plan) => $plan->subscriptions()->where('status', 'active')->count())
            ->editColumn('is_active', function ($plan) {
                return $plan->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', function ($plan) {
                $id = $plan->id;
                return <<<HTML
                    <div class="d-flex gap-1">
                        <a href="javascript:void(0)" onclick="managePlanFeatures({$id})" class="btn btn-sm btn-outline-info" title="Manage Features">
                            <i class="fa fa-cog"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="editPlan({$id})" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="deletePlan({$id})" class="btn btn-sm btn-outline-danger">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                HTML;
            })
            ->rawColumns(['action', 'is_active'])
            ->make(true);
    }

    public function manageFeatures($id)
    {
        // apply permissions
        abort_if(!hasPermission('view features'), 403, __('You are not authorized to view plans'));
        $plan = $this->planService->getPlan($id);
        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => __('Plan not found')
            ], 404);
        }

        $allFeatures = $this->planService->getAllFeatures();
        $planFeatures = $plan->planFeatures->keyBy('feature_id');

        return response()->json([
            'success' => true,
            'html' => view('backend.dashboards.admin.pages.plans.manage-features', compact('plan', 'allFeatures', 'planFeatures'))->render()
        ]);
    }

    public function addFeature(Request $request, $planId)
    {
        // apply permissions
        abort_if(!hasPermission('create feature'), 403, __('You are not authorized to view features'));
        $plan = Plan::findOrFail($planId);

        $validated = $request->validate([
            'feature_id' => 'required|exists:features_master,id',
            'is_enabled' => 'boolean',
            'value' => 'nullable|string',
            'is_limited' => 'boolean',
        ]);

        // Check if feature already exists
        if ($plan->planFeatures()->where('feature_id', $validated['feature_id'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Feature already exists in this plan')
            ], 422);
        }

        try {
            $plan->planFeatures()->create([
                'feature_id' => $validated['feature_id'],
                'is_enabled' => $validated['is_enabled'] ?? true,
                'value' => $validated['value'] ?? null,
                'is_limited' => $validated['is_limited'] ?? false,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('Feature added successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function updateFeature(Request $request, $planId, $featureId)
    {
        // apply permissions
        abort_if(!hasPermission('update feature'), 403, __('You are not authorized to update feature'));
        $plan = Plan::findOrFail($planId);
        $planFeature = $plan->planFeatures()->where('feature_id', $featureId)->firstOrFail();

        // Accept all fields as optional since we update one at a time
        $validated = $request->validate([
            'is_enabled' => 'nullable|boolean',
            'value' => 'nullable|string',
            'is_limited' => 'nullable|boolean',
        ]);

        try {
            // Build update array - only update fields that are provided
            $updateData = [];

            if ($request->has('is_enabled')) {
                $updateData['is_enabled'] = filter_var($request->input('is_enabled'), FILTER_VALIDATE_BOOLEAN);
            }

            if ($request->has('is_limited')) {
                $updateData['is_limited'] = filter_var($request->input('is_limited'), FILTER_VALIDATE_BOOLEAN);
            }

            if ($request->has('value')) {
                $updateData['value'] = $request->input('value');
            }

            $planFeature->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => __('Feature updated successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function deleteFeature($planId, $featureId)
    {
        // apply permissions
        abort_if(!hasPermission('delete feature'), 403, __('You are not authorized to delete feature'));
        $plan = Plan::findOrFail($planId);
        $planFeature = $plan->planFeatures()->where('feature_id', $featureId)->firstOrFail();

        try {
            $planFeature->delete();

            return response()->json([
                'status' => 'success',
                'message' => __('Feature removed successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create plan'), 403, __('You are not authorized to create plan'));
        $features = $this->planService->getAllFeatures();
        return response()->json([
            'success' => true,
            'html' => view('backend.dashboards.admin.pages.plans.create', compact('features'))->render()
        ]);
    }

    public function store(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('create plan'), 403, __('You are not authorized to create plan'));
        $validated = $request->validate([
            'plan_type' => 'required|in:doctor,clinic,supplier',
            'level' => 'required|in:free,basic,advanced,vip',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        // Prevent duplicate plan_type + level combinations
        $duplicateExists = Plan::where('plan_type', $validated['plan_type'])
            ->where('level', $validated['level'])
            ->exists();

        if ($duplicateExists) {
            return response()->json([
                'status' => 'error',
                'message' => __('A plan with this type and level already exists.'),
            ], 422);
        }

        try {
            $plan = $this->planService->createPlan(
                [
                    'plan_type' => $validated['plan_type'],
                    'level' => $validated['level'],
                    'name' => $validated['name'],
                    'price' => $validated['price'],
                    'duration_in_days' => $validated['duration_in_days'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                    'description' => $validated['description'] ?? null,
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => __('Plan created successfully')
            ]);
        } catch (QueryException $e) {
            // Handle unique constraint or other DB-level issues gracefully
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => 'error',
                    'message' => __('A plan with this type and level already exists.'),
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => __('Failed to create plan.'),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to create plan.'),
            ], 422);
        }
    }

    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update plan'), 403, __('You are not authorized to update plan'));
        $plan = $this->planService->getPlan($id);
        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => __('Plan not found')
            ], 404);
        }

        $features = $this->planService->getAllFeatures();
        return response()->json([
            'success' => true,
            'html' => view('backend.dashboards.admin.pages.plans.edit', compact('plan', 'features'))->render()
        ]);
    }

    public function update(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update plan'), 403, __('You are not authorized to update plan'));
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'plan_type' => 'required|in:doctor,clinic,supplier',
            'level' => 'required|in:free,basic,advanced,vip',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        // Prevent duplicate plan_type + level combinations (excluding current plan)
        $duplicateExists = Plan::where('plan_type', $validated['plan_type'])
            ->where('level', $validated['level'])
            ->where('id', '!=', $plan->id)
            ->exists();

        if ($duplicateExists) {
            return response()->json([
                'status' => 'error',
                'message' => __('A plan with this type and level already exists.'),
            ], 422);
        }

        try {
            $plan = $this->planService->updatePlan(
                $plan,
                [
                    'plan_type' => $validated['plan_type'],
                    'level' => $validated['level'],
                    'name' => $validated['name'],
                    'price' => $validated['price'],
                    'duration_in_days' => $validated['duration_in_days'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                    'description' => $validated['description'] ?? null,
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => __('Plan updated successfully')
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => 'error',
                    'message' => __('A plan with this type and level already exists.'),
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => __('Failed to update plan.'),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to update plan.'),
            ], 422);
        }
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete plan'), 403, __('You are not authorized to delete plan'));
        try {
            $plan = Plan::findOrFail($id);
            $this->planService->deletePlan($plan);

            return response()->json([
                'status' => 'success',
                'message' => __('Plan deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
