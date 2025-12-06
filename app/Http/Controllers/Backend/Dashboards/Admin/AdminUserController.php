<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Interfaces\Admin\AdminUserRepositoryInterface;
use App\Models\Admin;
use App\Models\Role;

class AdminUserController extends Controller
{
    protected $adminUserRepo;

    public function __construct(AdminUserRepositoryInterface $adminUserRepo)
    {
        $this->adminUserRepo = $adminUserRepo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view admin users'), 403, __('You are not authorized to view admin users'));
        return view('backend.dashboards.admin.pages.admin-users.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view admin users'), 403, __('You are not authorized to view admin users'));
        return $this->adminUserRepo->data();
    }

    public function store(StoreAdminUserRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create admin user'), 403, __('You are not authorized to create admin users'));
        $this->adminUserRepo->store($request->validated());
        return $this->jsonResponse('success', __('Admin user created successfully'));
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view admin users'), 403, __('You are not authorized to view admin user'));
        $adminUser = $this->adminUserRepo->show($id);
        return request()->ajax()
            ? response()->json($adminUser->load('roles'))
            : view('backend.dashboards.admin.pages.admin-users.show', compact('adminUser'));
    }

    public function update(UpdateAdminUserRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update admin user'), 403, __('You are not authorized to update admin user'));
        $this->adminUserRepo->update($request->validated(), $id);
        return $this->jsonResponse('success', __('Admin user updated successfully'));
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete admin user'), 403, __('You are not authorized to delete admin user'));
        try {
            $this->adminUserRepo->destroy($id);
            return $this->jsonResponse('success', __('Admin user deleted successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash admin users'), 403, __('You are not authorized to view trash admin users'));
        return view('backend.dashboards.admin.pages.admin-users.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash admin users'), 403, __('You are not authorized to view trash admin users'));
        return $this->adminUserRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore admin user'), 403, __('You are not authorized to restore admin user'));
        $this->adminUserRepo->restore($id);
        return $this->jsonResponse('success', __('Admin user restored successfully'));
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete admin user'), 403, __('You are not authorized to force delete admin user'));
        try {
            $this->adminUserRepo->forceDelete($id);
            return $this->jsonResponse('success', __('Admin user permanently deleted successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        // apply permissions
        abort_if(!hasPermission('toggle admin user status'), 403, __('You are not authorized to toggle admin user status'));
        $this->adminUserRepo->toggleStatus($id);
        return $this->jsonResponse('success', __('Admin user status updated successfully'));
    }

    public function roles()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        return response()->json($roles);
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message], $status === 'error' ? 400 : 200);
        }

        return redirect()->back()->with($status, $message);
    }
}
