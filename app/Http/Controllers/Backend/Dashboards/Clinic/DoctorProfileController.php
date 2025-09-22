<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\DoctorProfile\DoctorProfileStoreRequest;
use App\Http\Requests\Clinic\DoctorProfile\DoctorProfileUpdateRequest;
use App\Interfaces\Clinic\DoctorProfileRepositoryInterface;
use App\Models\DoctorProfile;

class DoctorProfileController extends Controller
{
    protected $profileRepo;

    public function __construct(DoctorProfileRepositoryInterface $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    public function index()
    {
        $userProfile = $this->profileRepo->getUserProfile(auth('clinic')->id());
        return view('backend.dashboards.clinic.pages.doctor-profiles.index', compact('userProfile'));
    }

    public function data()
    {
        return $this->profileRepo->data();
    }

    public function create()
    {
        // Check if user already has a profile
        $existingProfile = $this->profileRepo->getUserProfile(auth('clinic')->id());
        if ($existingProfile) {
            return redirect()->route('clinic.doctor-profiles.show', $existingProfile->id)
                ->with('info', 'You already have a profile. You can edit it below.');
        }

        return view('backend.dashboards.clinic.pages.doctor-profiles.create');
    }

    public function store(DoctorProfileStoreRequest $request)
    {
        // Check if user already has a profile
        $existingProfile = $this->profileRepo->getUserProfile(auth('clinic')->id());
        if ($existingProfile) {
            return $this->jsonResponse('error', 'You already have a profile. Please edit your existing profile.');
        }

        $this->profileRepo->store($request->validated());
        return $this->jsonResponse('success', __('Profile created successfully'));
    }

    public function show($id)
    {
        $profile = $this->profileRepo->show($id);
        return request()->ajax()
            ? response()->json($profile)
            : view('backend.dashboards.clinic.pages.doctor-profiles.show', compact('profile'));
    }

    public function edit($id)
    {
        $profile = $this->profileRepo->show($id);

        if (!$profile->canBeEdited()) {
            return redirect()->route('clinic.doctor-profiles.show', $id)
                ->with('error', 'Profile cannot be edited in current status.');
        }

        return view('backend.dashboards.clinic.pages.doctor-profiles.edit', compact('profile'));
    }

    public function update(DoctorProfileUpdateRequest $request, $id)
    {
        try {
            $this->profileRepo->update($request->validated(), $id);
            return $this->jsonResponse('success', __('Profile updated successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->profileRepo->destroy($id);
            return $this->jsonResponse('success', __('Profile deleted successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function submit($id)
    {
        try {
            $this->profileRepo->submitForReview($id);
            return $this->jsonResponse('success', __('Profile submitted for review successfully'));
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
