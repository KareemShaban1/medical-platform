<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
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
        return view('backend.dashboards.clinic.pages.users.index');
    }

    public function data()
    {
        return $this->userRepo->data();
    }

    public function store(StoreUserRequest $request)
    {

        $this->userRepo->store($request->validated());
        return $this->jsonResponse('success', __('User created successfully'));
    }

    public function show($id)
    {
        $user = $this->userRepo->show($id);
        return request()->ajax()
        ? response()->json($user->load('roles'))
        : view('backend.dashboards.clinic.pages.users.show', compact('user'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $this->userRepo->update($request->validated(), $id);
        return $this->jsonResponse('success', __('User updated successfully'));
    }

    public function destroy($id)
    {
        $this->userRepo->destroy($id);
        return $this->jsonResponse('success', __('User deleted successfully'));
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.users.trash');
    }

    public function trashData()
    {
        return $this->userRepo->trashData();
    }

    public function restore($id)
    {
        $this->userRepo->restore($id);
        return $this->jsonResponse('success', __('User restored successfully'));
    }

    public function forceDelete($id)
    {
        $this->userRepo->forceDelete($id);
        return $this->jsonResponse('success', __('User permanently deleted successfully'));
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
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
