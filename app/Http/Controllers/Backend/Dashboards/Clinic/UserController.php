<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreClinicUserRequest;
use App\Http\Requests\User\UpdateClinicUserRequest;
use App\Interfaces\Clinic\UserRepositoryInterface;
use App\Models\ClinicUser;
use App\Models\Role;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view users'), 403, __('You are not authorized to view users'));

        return view('backend.dashboards.clinic.pages.users.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view users'), 403, __('You are not authorized to view users'));

        return $this->userRepo->data();
    }

    public function store(StoreClinicUserRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create user'), 403, __('You are not authorized to create users'));

        $this->userRepo->store($request->validated());
        return $this->jsonResponse('success', __('User created successfully'));
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view user'), 403, __('You are not authorized to view user'));

        $user = $this->userRepo->show($id);
        return request()->ajax()
        ? response()->json($user->load('roles'))
        : view('backend.dashboards.clinic.pages.users.show', compact('user'));
    }

    public function update(UpdateClinicUserRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update user'), 403, __('You are not authorized to update user'));

        $this->userRepo->update($request->validated(), $id);
        return $this->jsonResponse('success', __('User updated successfully'));
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete user'), 403, __('You are not authorized to delete user'));

        try {
            $this->userRepo->destroy($id);
            return $this->jsonResponse('success', __('User deleted successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash users'), 403, __('You are not authorized to view trash users'));

        return view('backend.dashboards.clinic.pages.users.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash users'), 403, __('You are not authorized to view trash users'));

        return $this->userRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore user'), 403, __('You are not authorized to restore user'));

        $this->userRepo->restore($id);
        return $this->jsonResponse('success', __('User restored successfully'));
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete user'), 403, __('You are not authorized to force delete user'));

        try {
            $this->userRepo->forceDelete($id);
            return $this->jsonResponse('success', __('User permanently deleted successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $this->userRepo->toggleStatus($id);
        return $this->jsonResponse('success', __('User status updated successfully'));
    }

    public function roles()
    {
        $roles = Role::where('guard_name', 'clinic')->where('team_id', auth('clinic')->user()->clinic_id)->get();
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
