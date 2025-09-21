<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\DoctorProfileRepositoryInterface;
use App\Models\DoctorProfile;
use App\Notifications\ProfileSubmittedForReview;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class DoctorProfileRepository implements DoctorProfileRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $profiles = DoctorProfile::with(['clinicUser'])
            ->forClinicUser(auth('clinic')->id());

        return datatables()->of($profiles)
            ->addColumn('profile_photo', fn($item) => $this->profilePhoto($item))
            ->addColumn('name', fn($item) => $item->name)
            ->editColumn('status', fn($item) => $item->status_badge)
            ->addColumn('action', fn($item) => $this->profileActions($item))
            ->rawColumns(['profile_photo', 'status', 'action'])
            ->make(true);
    }

    public function getUserProfile($clinicUserId)
    {
        return DoctorProfile::forClinicUser($clinicUserId)->first();
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;
            $data['clinic_user_id'] = auth('clinic')->id();

            $profile = DoctorProfile::create($data);

            if (!empty($data['profile_photo'])) {
                $profile->addMedia($data['profile_photo'])
                    ->toMediaCollection('profile_photo');
            }

            return $profile;
        });
    }

    public function show($id)
    {
        return DoctorProfile::with(['clinicUser', 'reviewer'])
            ->forClinicUser(auth('clinic')->id())
            ->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $profile = DoctorProfile::forClinicUser(auth('clinic')->id())->findOrFail($id);

            if (!$profile->canBeEdited()) {
                throw new \Exception('Profile cannot be edited in current status');
            }

            $data = $request;
            $profile->update($data);

            if (!empty($data['profile_photo'])) {
                $profile->clearMediaCollection('profile_photo');
                $profile->addMedia($data['profile_photo'])
                    ->toMediaCollection('profile_photo');
            }

            return $profile;
        });
    }

    public function submitForReview($id)
    {
        return DB::transaction(function () use ($id) {
            $profile = DoctorProfile::forClinicUser(auth('clinic')->id())->findOrFail($id);

            if (!in_array($profile->status, [DoctorProfile::STATUS_DRAFT, DoctorProfile::STATUS_REJECTED])) {
                throw new \Exception('Profile cannot be submitted in current status');
            }

            $profile->submitForReview();

            // Notify all admins
            $admins = Admin::all();
            Notification::send($admins, new ProfileSubmittedForReview($profile));

            return $profile;
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $profile = DoctorProfile::forClinicUser(auth('clinic')->id())->findOrFail($id);

            if (!in_array($profile->status, [DoctorProfile::STATUS_DRAFT, DoctorProfile::STATUS_REJECTED])) {
                throw new \Exception('Profile cannot be deleted in current status');
            }

            $profile->clearMediaCollection('profile_photo');
            $profile->delete();

            return $profile;
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function profilePhoto($item): string
    {
        if ($item->profile_photo_url) {
            return '<img src="' . $item->profile_photo_url . '" alt="Profile Photo" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">';
        }
        return '<div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: white;"><i class="mdi mdi-account"></i></div>';
    }

    private function profileActions($item): string
    {
        $showUrl = route('clinic.doctor-profiles.show', $item->id);
        $actions = '<div class="d-flex gap-2">';

        $actions .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';

        if ($item->canBeEdited()) {
            $actions .= '<button onclick="editProfile(' . $item->id . ')" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>';
            $actions .= '<button onclick="deleteProfile(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';
        }

        if (in_array($item->status, [DoctorProfile::STATUS_DRAFT, DoctorProfile::STATUS_REJECTED])) {
            $actions .= '<button onclick="submitProfile(' . $item->id . ')" class="btn btn-sm btn-primary" title="Submit for Review"><i class="fa fa-paper-plane"></i></button>';
        }

        $actions .= '</div>';
        return $actions;
    }
}
