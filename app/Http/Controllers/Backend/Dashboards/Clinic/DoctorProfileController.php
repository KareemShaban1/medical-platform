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
        return view('backend.dashboards.clinic.pages.doctor-profiles.index');
    }

    public function data()
    {
        return $this->profileRepo->data();
    }

    public function create()
    {
        $specialities = Speciality::orderBy('name_en')->get();
        return view('backend.dashboards.clinic.pages.doctor-profiles.create', compact('specialities'));
    }

    public function store(DoctorProfileStoreRequest $request)
    {
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

        $specialities = Speciality::orderBy('name_en')->get();
        return view('backend.dashboards.clinic.pages.doctor-profiles.edit', compact('profile','specialities'));
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

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.doctor-profiles.trash');
    }

    public function trashData()
    {
        return $this->profileRepo->trashData();
    }

    public function restore($id)
    {
        try {
            $this->profileRepo->restore($id);
            return $this->jsonResponse('success', __('Profile restored successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
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
