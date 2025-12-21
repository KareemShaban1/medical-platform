<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureMaster;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeatureMasterController extends Controller
{
    public function index(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view features'), 403, __('You are not authorized to view features'));
        if ($request->has('modal') && $request->modal === 'create') {
            return response()->json([
                'success' => true,
                'html' => view('backend.dashboards.admin.pages.features.create')->render()
            ]);
        }
        return view('backend.dashboards.admin.pages.features.index');
    }

    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create feature'), 403, __('You are not authorized to create feature'));
        return response()->json([
            'success' => true,
            'html' => view('backend.dashboards.admin.pages.features.create')->render()
        ]);
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view features'), 403, __('You are not authorized to view features'));
        return DataTables::of(FeatureMaster::query())
            ->editColumn('is_active', function ($feature) {
                return $feature->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', function ($feature) {
                $id = $feature->id;
                return <<<HTML
                    <div class="d-flex gap-1">
                        <a href="javascript:void(0)" onclick="editFeature({$id})" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="deleteFeature({$id})" class="btn btn-sm btn-outline-danger">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                HTML;
            })
            ->rawColumns(['action', 'is_active'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('create feature'), 403, __('You are not authorized to create feature'));
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:features_master,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:255',
            'value_type' => 'required|in:boolean,integer,string,json',
            'is_active' => 'boolean',
        ]);

        FeatureMaster::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => __('Feature created successfully')
        ]);
    }

    public function update(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update feature'), 403, __('You are not authorized to update feature'));
        $feature = FeatureMaster::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:features_master,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:255',
            'value_type' => 'required|in:boolean,integer,string,json',
            'is_active' => 'boolean',
        ]);

        $feature->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => __('Feature updated successfully')
        ]);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete feature'), 403, __('You are not authorized to delete feature'));
        $feature = FeatureMaster::findOrFail($id);

        // Check if feature is used in any plan
        if ($feature->planFeatures()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Cannot delete feature that is used in plans')
            ], 422);
        }

        $feature->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Feature deleted successfully')
        ]);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view features'), 403, __('You are not authorized to view feature'));
        $feature = FeatureMaster::findOrFail($id);
        return response()->json([
            'success' => true,
            'html' => view('backend.dashboards.admin.pages.features.edit', compact('feature'))->render()
        ]);
    }
}
