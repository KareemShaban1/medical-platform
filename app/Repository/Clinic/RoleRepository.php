<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleRepository implements RoleRepositoryInterface
{
    protected $guard = 'clinic';

    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        $roles = Role::where('guard_name', $this->guard)->get();
        $permissions = Permission::where('guard_name', $this->guard)->get();

        return view('backend.dashboards.clinic.pages.roles.index', compact('roles', 'permissions'));
    }

    public function data()
    {
        $roles = Role::with('permissions')
            ->where('guard_name', $this->guard)
            ->get();

        return DataTables::of($roles)
            ->addColumn('actions', fn($role) => $this->roleActions($role))
            ->addColumn('permissions_count', fn($role) => $role->permissions->count())
            ->rawColumns(['actions', 'permissions_count'])
            ->make(true);
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', $this->guard)->get();
        return view('backend.dashboards.clinic.pages.roles.create', compact('permissions'));
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => $this->guard
            ]);

            if ($request->permissions) {
                $permissions = Permission::whereIn('id', $request->permissions)
                    ->where('guard_name', $this->guard)
                    ->get();
                $role->syncPermissions($permissions);
            }

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Role created successfully'));
            }

            return redirect()->route('clinic.roles.index')->with('success', __('Role created successfully'));
        });
    }

    public function show($id)
    {
        return Role::where('guard_name', $this->guard)->findOrFail($id);
    }

    public function edit($id)
    {
        $role = Role::where('guard_name', $this->guard)->with('permissions')->findOrFail($id);
        $permissions = Permission::where('guard_name', $this->guard)->get();

        if (request()->ajax()) {
            return response()->json([
                'role' => $role,
                'permissions' => $permissions,
            ]);
        }

        return view('backend.dashboards.clinic.pages.roles.edit', compact('role', 'permissions'));
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $role = Role::where('guard_name', $this->guard)->findOrFail($id);

            $role->update(['name' => $request->name]);

            if ($request->permissions) {
                $permissions = Permission::whereIn('id', $request->permissions)
                    ->where('guard_name', $this->guard)
                    ->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Role updated successfully'));
            }

            return redirect()->route('clinic.roles.index')->with('success', __('Role updated successfully'));
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $role = Role::where('guard_name', $this->guard)->findOrFail($id);
            $role->delete();

            if (request()->ajax()) {
                return $this->jsonResponse('success', __('Role moved to trash successfully'));
            }

            return redirect()->route('clinic.roles.index')->with('success', __('Role moved to trash successfully'));
        });
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.roles.trash');
    }

    public function trashData()
    {
        $roles = Role::onlyTrashed()->where('guard_name', $this->guard);

        return DataTables::of($roles)
            ->addColumn('status', fn() => '<span class="badge bg-secondary">Trashed</span>')
            ->addColumn('action', fn($role) => $this->trashActions($role))
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        return DB::transaction(function () use ($id) {
            $role = Role::onlyTrashed()->where('guard_name', $this->guard)->findOrFail($id);
            $role->restore();

            return redirect()->route('clinic.roles.index')->with('success', __('Role restored successfully'));
        });
    }

    public function forceDelete($id)
    {
        return DB::transaction(function () use ($id) {
            $role = Role::onlyTrashed()->where('guard_name', $this->guard)->findOrFail($id);
            $role->forceDelete();

            if (request()->ajax()) {
                return $this->jsonResponse('success', __('Role permanently deleted successfully'));
            }

            return redirect()->route('clinic.roles.trash')->with('success', __('Role permanently deleted successfully'));
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function roleActions($role): string
    {
        $permissions = $role->permissions->pluck('id')->toArray();
        $permissionsJson = htmlspecialchars(json_encode($permissions), ENT_QUOTES, 'UTF-8');

        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editRole({$role->id}, '{$role->name}', {$permissionsJson})" class="btn btn-sm btn-info">
                <i class="fa fa-edit"></i>
            </button>
            <button onclick="deleteRole({$role->id})" class="btn btn-sm btn-danger">
                <i class="fa fa-trash"></i>
            </button>
        </div>
        HTML;
    }

    private function trashActions($role): string
    {
        return <<<HTML
        <button class="btn btn-sm btn-success" onclick="restoreRole({$role->id})">
            <i class="mdi mdi-restore"></i> Restore
        </button>
        <button class="btn btn-sm btn-danger" onclick="forceDeleteRole({$role->id})">
            <i class="mdi mdi-delete-forever"></i> Delete
        </button>
        HTML;
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
