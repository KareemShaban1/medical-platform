<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\AdminUserRepositoryInterface;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserRepository implements AdminUserRepositoryInterface
{
    public function data()
    {
        $adminUsers = Admin::query();

        setPermissionsTeamId(Admin::TeamId);
        return datatables()->of($adminUsers)
            ->addColumn('roles', fn($item) => $this->adminUserRoles($item))
            ->editColumn('status', fn($item) => $this->adminUserStatus($item))
            ->editColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('action', fn($item) => $this->adminUserActions($item))
            ->rawColumns(['roles', 'status', 'action'])
            ->make(true);
    }

    public function store(array $data)
    {
        try {
            DB::beginTransaction();

            $adminUser = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            if (isset($data['role'])) {
                $role = Role::where('name', $data['role'])
                    ->where('guard_name', 'admin')
                    ->first();

                if ($role) {
                    setPermissionsTeamId(Admin::TeamId);
                    $adminUser->assignRole($role);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show($id)
    {
        setPermissionsTeamId(Admin::TeamId);
        return Admin::with('roles')->findOrFail($id);
    }

    public function update(array $data, $id)
    {
        try {
            DB::beginTransaction();

            $adminUser = Admin::findOrFail($id);

            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $adminUser->update($updateData);

            // Update role
            if (isset($data['role'])) {
                setPermissionsTeamId(Admin::TeamId);
                $adminUser->roles()->detach();
                $role = Role::where('name', $data['role'])
                    ->where('guard_name', 'admin')
                    ->first();

                if ($role) {
                    $adminUser->assignRole($role);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        if (auth('admin')->user()->id == $id) {
            throw new \Exception(__('You cannot delete yourself'));
        };

        $adminUser = Admin::findOrFail($id);
        $adminUser->delete();
    }

    public function trashData()
    {
        setPermissionsTeamId(Admin::TeamId);
        $adminUsers = Admin::onlyTrashed()->with('roles');

        return datatables()->of($adminUsers)
            ->addColumn('roles', fn($item) => $this->adminUserRoles($item))
            ->editColumn('status', fn($item) => $this->adminUserStatus($item))
            ->editColumn('deleted_at', fn($item) => $item->deleted_at->format('Y-m-d H:i')  )
            ->addColumn('action', fn($item) => $this->adminUserTrashActions($item))
            ->rawColumns(['roles', 'status', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        $adminUser = Admin::onlyTrashed()->findOrFail($id);
        $adminUser->restore();
    }

    public function forceDelete($id)
    {
        $adminUser = Admin::onlyTrashed()->findOrFail($id);
        $adminUser->forceDelete();
    }

    public function toggleStatus($id)
    {
        $adminUser = Admin::findOrFail($id);
        $adminUser->update(['status' => !$adminUser->status]);
    }

    private function adminUserRoles($item): string
    {
        $roles = $item->roles->pluck('name')->toArray();

        if (empty($roles)) {
            return '<span class="badge bg-secondary">No Role</span>';
        }

        $badges = '';
        foreach ($roles as $role) {
            $badges .= '<span class="badge bg-primary me-1">' . ucfirst($role) . '</span>';
        }

        return $badges;
    }

    private function adminUserStatus($item): string
    {
        $status = $item->status ?? false;
        $badgeClass = $status ? 'bg-success' : 'bg-danger';
        $badgeText = $status ? 'Active' : 'Inactive';

        return '<span class="badge ' . $badgeClass . '">' . $badgeText . '</span>';
    }

    private function adminUserActions($item): string
    {
        $actions = '';

        // Show action
        $actions .= '<button class="btn btn-sm btn-info me-1" onclick="showAdminUser(' . $item->id . ')">
                        <i class="fa fa-eye"></i>
                    </button>';

        // Edit action
        $actions .= '<button class="btn btn-sm btn-warning me-1" onclick="editAdminUser(' . $item->id . ')">
                        <i class="fa fa-edit"></i>
                    </button>';

        // Toggle status action
        $statusIcon = $item->status ? 'fa-toggle-on' : 'fa-toggle-off';
        $statusClass = $item->status ? 'btn-success' : 'btn-secondary';
        $actions .= '<button class="btn btn-sm ' . $statusClass . ' me-1" onclick="toggleAdminUserStatus(' . $item->id . ')">
                        <i class="fa ' . $statusIcon . '"></i>
                    </button>';

        // Delete action
        $actions .= '<button class="btn btn-sm btn-danger" onclick="deleteAdminUser(' . $item->id . ')">
                        <i class="fa fa-trash"></i>
                    </button>';

        return $actions;
    }

    private function adminUserTrashActions($item): string
    {
        $actions = '';

        // Restore action
        $actions .= '<button class="btn btn-sm btn-success me-1" onclick="restoreAdminUser(' . $item->id . ')">
                        <i class="fa fa-undo"></i>
                    </button>';

        // Force delete action
        $actions .= '<button class="btn btn-sm btn-danger" onclick="forceDeleteAdminUser(' . $item->id . ')">
                        <i class="fa fa-trash"></i>
                    </button>';

        return $actions;
    }
}
