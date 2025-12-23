<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\DoctorProfile\DoctorProfileStoreRequest;
use App\Http\Requests\Clinic\DoctorProfile\DoctorProfileUpdateRequest;
use App\Interfaces\Clinic\DoctorProfileRepositoryInterface;
use App\Models\DoctorProfile;
use App\Models\Speciality;

class DoctorProfileController extends Controller
{
    protected $profileRepo;

    public function __construct(DoctorProfileRepositoryInterface $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view doctor profiles'), 403, __('You are not authorized to view doctor profiles'));

        return view('backend.dashboards.clinic.pages.doctor-profiles.index');
    }

    public function myProfile()
    {
        // apply permissions
        abort_if(!hasPermission('view doctor profiles'), 403, __('You are not authorized to view my doctor profile'));

        $user = auth('clinic')->user();
        $userProfile = $this->profileRepo->getUserProfile($user->id);
        return view('backend.dashboards.clinic.pages.doctor-profiles.index-old', compact('userProfile'));
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view doctor profiles'), 403, __('You are not authorized to view doctor profiles'));

        return $this->profileRepo->data();
    }

    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create doctor profile'), 403, __('You are not authorized to create doctor profile'));

        $specialities = Speciality::orderBy('name_en')->get();
        $user = auth('clinic')->user();
        return view('backend.dashboards.clinic.pages.doctor-profiles.create', compact('specialities', 'user'));
    }

    public function store(DoctorProfileStoreRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create doctor profile'), 403, __('You are not authorized to create doctor profile'));

        $this->profileRepo->store($request->validated());
        return $this->jsonResponse('success', __('Profile created successfully'));
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view doctor profiles'), 403, __('You are not authorized to view doctor profile'));

        $profile = $this->profileRepo->show($id);
        return request()->ajax()
            ? response()->json($profile)
            : view('backend.dashboards.clinic.pages.doctor-profiles.show', compact('profile'));
    }

    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update doctor profile'), 403, __('You are not authorized to edit doctor profile'));

        $profile = $this->profileRepo->show($id);

        if (!$profile->canBeEdited()) {
            return redirect()->route('clinic.doctor-profiles.show', $id)
                ->with('error', 'Profile cannot be edited in current status.');
        }

        $specialities = Speciality::orderBy('name_en')->get();
        return view('backend.dashboards.clinic.pages.doctor-profiles.edit', compact('profile','specialities'));
    }

    public function update(DoctorProfileUpdateRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update doctor profile'), 403, __('You are not authorized to update doctor profile'));

        try {
            $this->profileRepo->update($request->validated(), $id);
            return $this->jsonResponse('success', __('Profile updated successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete doctor profile'), 403, __('You are not authorized to delete doctor profile'));

        try {
            $this->profileRepo->destroy($id);
            return $this->jsonResponse('success', __('Profile deleted successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function submit($id)
    {
        // apply permissions
        abort_if(!hasPermission('submit doctor profile'), 403, __('You are not authorized to submit doctor profile'));

        try {
            $this->profileRepo->submitForReview($id);
            return $this->jsonResponse('success', __('Profile submitted for review successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash doctor profiles'), 403, __('You are not authorized to view trash doctor profiles'));

        return view('backend.dashboards.clinic.pages.doctor-profiles.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash doctor profiles'), 403, __('You are not authorized to view trash doctor profiles'));

        return $this->profileRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore doctor profile'), 403, __('You are not authorized to restore doctor profile'));

        try {
            $this->profileRepo->restore($id);
            return $this->jsonResponse('success', __('Profile restored successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete doctor profile'), 403, __('You are not authorized to force delete doctor profile'));

        try {
            $this->profileRepo->forceDelete($id);
            return $this->jsonResponse('success', __('Profile permanently deleted'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
