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
        $clinicId = auth('clinic')->user()->clinic_id;

        $profiles = DoctorProfile::with(['clinicUser','speciality'])
            ->whereHas('clinicUser', function($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            });

        return datatables()->of($profiles)
            ->addColumn('profile_photo', fn($item) => $this->profilePhoto($item))
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('phone', fn($item) => $item->phone ?? 'N/A')
            ->addColumn('speciality', fn($item) => $item->speciality?->name_en ?? 'N/A')
            ->addColumn('years_experience', fn($item) => $item->years_experience ?? 'N/A')
            ->editColumn('status', fn($item) => $item->status_badge)
            ->addColumn('action', fn($item) => $this->profileActions($item))
            ->rawColumns(['profile_photo', 'status', 'action'])
            ->make(true);
    }    public function getUserProfile($clinicUserId)
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
        $clinicId = auth('clinic')->user()->clinic_id;

        return DoctorProfile::with(['clinicUser', 'reviewer','speciality'])
            ->whereHas('clinicUser', function($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            })
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
            $clinicId = auth('clinic')->user()->clinic_id;
            $profile = DoctorProfile::whereHas('clinicUser', function($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            })->findOrFail($id);

            $profile->delete();

            return $profile;
        });
    }

    public function trashData()
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $profiles = DoctorProfile::onlyTrashed()
            ->with(['clinicUser','speciality'])
            ->whereHas('clinicUser', function($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            });

        return datatables()->of($profiles)
            ->addColumn('profile_photo', fn($item) => $this->profilePhoto($item))
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('phone', fn($item) => $item->phone ?? 'N/A')
            ->addColumn('speciality', fn($item) => $item->speciality?->name_en ?? 'N/A')
            ->addColumn('years_experience', fn($item) => $item->years_experience ?? 'N/A')
            ->editColumn('status', fn($item) => $item->status_badge)
            ->addColumn('deleted_at', fn($item) => $item->deleted_at?->format('Y-m-d H:i'))
            ->addColumn('action', fn($item) => $this->trashActionButtons($item))
            ->rawColumns(['profile_photo', 'status', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        return DB::transaction(function () use ($id) {
            $clinicId = auth('clinic')->user()->clinic_id;
            $profile = DoctorProfile::onlyTrashed()
                ->whereHas('clinicUser', function($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                })->findOrFail($id);

            $profile->restore();

            return $profile;
        });
    }

    public function forceDelete($id)
    {
        return DB::transaction(function () use ($id) {
            $clinicId = auth('clinic')->user()->clinic_id;
            $profile = DoctorProfile::onlyTrashed()
                ->whereHas('clinicUser', function($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                })->findOrFail($id);

            $profile->clearMediaCollection('profile_photo');
            $profile->forceDelete();

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
        $actions = '<div class="d-flex gap-2">';

        if (hasPermission('view doctor profiles')) {
            $showUrl = route('clinic.doctor-profiles.show', $item->id);
            $actions .= '<a href="' . $showUrl . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>';
        }

        if (hasPermission('update doctor profile') && $item->canBeEdited()) {
            $editUrl = route('clinic.doctor-profiles.edit', $item->id);
            $actions .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-edit"></i></a>';
        }

        if (hasPermission('submit doctor profile') && in_array($item->status, [DoctorProfile::STATUS_DRAFT, DoctorProfile::STATUS_REJECTED])) {
            $actions .= '<button onclick="submitProfile(' . $item->id . ')" class="btn btn-sm btn-success" title="Submit for Review"><i class="fa fa-paper-plane"></i></button>';
        }

        if (hasPermission('delete doctor profile')) {
            $actions .= '<button onclick="deleteProfile(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';
        }

        $actions .= '</div>';
        return $actions;
    }

    private function trashActionButtons($item): string
    {
        $actions = '<div class="d-flex gap-2">';

        if (hasPermission('restore doctor profile')) {
            $actions .= '<button onclick="restoreProfile(' . $item->id . ')" class="btn btn-sm btn-success" title="Restore"><i class="fa fa-undo"></i> Restore</button>';
        }

        if (hasPermission('force delete doctor profile')) {
            $actions .= '<button onclick="forceDeleteProfile(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete Forever"><i class="fa fa-trash"></i> Delete Forever</button>';
        }

        $actions .= '</div>';
        return $actions;
    }
}