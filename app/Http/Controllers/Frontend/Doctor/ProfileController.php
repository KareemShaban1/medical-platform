<?php

namespace App\Http\Controllers\Frontend\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\Speciality;
use App\Interfaces\Clinic\DoctorProfileRepositoryInterface;
use App\Http\Requests\Clinic\DoctorProfile\DoctorProfileStoreRequest;
use App\Http\Requests\Clinic\DoctorProfile\DoctorProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $profileRepo;

    public function __construct(DoctorProfileRepositoryInterface $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    public function index()
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->has_clinic) {
            return redirect()->route('home');
        }

        // Get or create doctor profile
        $profile = $this->profileRepo->getUserProfile($doctor->id);
        $specialities = Speciality::orderBy('name_en')->get();

        return view('frontend.doctor.profile.index', compact('doctor', 'profile', 'specialities'));
    }

    public function create()
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->has_clinic) {
            return redirect()->route('home');
        }

        // Check if user already has a profile
        $existingProfile = $this->profileRepo->getUserProfile($doctor->id);
        if ($existingProfile) {
            return redirect()->route('doctor.profile.index')
                ->with('info', 'You already have a profile. You can edit it below.');
        }

        $specialities = Speciality::orderBy('name_en')->get();
        return view('frontend.doctor.profile.create', compact('specialities'));
    }

    public function store(DoctorProfileStoreRequest $request)
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->has_clinic) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Check if user already has a profile
        $existingProfile = $this->profileRepo->getUserProfile($doctor->id);
        if ($existingProfile) {
            return response()->json(['status' => 'error', 'message' => 'You already have a profile. Please edit your existing profile.'], 422);
        }

        try {
            $this->profileRepo->store($request->validated());
            return response()->json(['status' => 'success', 'message' => __('Profile created successfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit()
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->has_clinic) {
            return redirect()->route('home');
        }

        $profile = $this->profileRepo->getUserProfile($doctor->id);

        if (!$profile) {
            return redirect()->route('doctor.profile.create');
        }

        if (!$profile->canBeEdited()) {
            return redirect()->route('doctor.profile.index')
                ->with('error', 'Profile cannot be edited in current status.');
        }

        $specialities = Speciality::orderBy('name_en')->get();
        return view('frontend.doctor.profile.edit', compact('profile', 'specialities'));
    }

    public function update(DoctorProfileUpdateRequest $request)
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->has_clinic) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $profile = $this->profileRepo->getUserProfile($doctor->id);

        if (!$profile) {
            return response()->json(['status' => 'error', 'message' => 'Profile not found'], 404);
        }

        try {
            $this->profileRepo->update($request->validated(), $profile->id);
            return response()->json(['status' => 'success', 'message' => __('Profile updated successfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function submit()
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->has_clinic) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $profile = $this->profileRepo->getUserProfile($doctor->id);

        if (!$profile) {
            return response()->json(['status' => 'error', 'message' => 'Profile not found'], 404);
        }

        try {
            $this->profileRepo->submitForReview($profile->id);
            return response()->json(['status' => 'success', 'message' => __('Profile submitted for review successfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

