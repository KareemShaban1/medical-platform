<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\DoctorProfileRepositoryInterface;
use App\Models\DoctorProfile;
use App\Notifications\ProfileApproved;
use App\Notifications\ProfileRejected;
use Illuminate\Support\Facades\DB;

class DoctorProfileRepository implements DoctorProfileRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $profiles = DoctorProfile::with(['clinicUser', 'reviewer']);

        return datatables()->of($profiles)
            ->addColumn('profile_photo', fn($item) => $this->profilePhoto($item))
            ->addColumn('doctor_name', fn($item) => $item->name)
            ->addColumn('clinic_user', fn($item) => $item->clinicUser->name ?? 'N/A')
            ->editColumn('status', fn($item) => $item->status_badge)
            ->addColumn('reviewed_by', fn($item) => $item->reviewer->name ?? 'N/A')
            ->addColumn('action', fn($item) => $this->profileActions($item))
            ->rawColumns(['profile_photo', 'status', 'action'])
            ->make(true);
    }

    public function pendingData()
    {
        $profiles = DoctorProfile::with(['clinicUser'])
            ->pending();

        return datatables()->of($profiles)
            ->addColumn('profile_photo', fn($item) => $this->profilePhoto($item))
            ->addColumn('doctor_name', fn($item) => $item->name)
            ->addColumn('clinic_user', fn($item) => $item->clinicUser->name ?? 'N/A')
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('specialties', fn($item) => $this->formatSpecialties($item))
            ->addColumn('action', fn($item) => $this->pendingActions($item))
            ->rawColumns(['profile_photo', 'specialties', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        return DoctorProfile::with(['clinicUser', 'reviewer'])->findOrFail($id);
    }

    public function approve($id)
    {
        return DB::transaction(function () use ($id) {
            $profile = DoctorProfile::findOrFail($id);

            if ($profile->status !== DoctorProfile::STATUS_PENDING) {
                throw new \Exception('Only pending profiles can be approved');
            }

            $profile->approve(auth('admin')->id());

            // Notify the clinic user
            $profile->clinicUser->notify(new ProfileApproved($profile));

            return $profile;
        });
    }

    public function reject($id, $reason)
    {
        return DB::transaction(function () use ($id, $reason) {
            $profile = DoctorProfile::findOrFail($id);

            if ($profile->status !== DoctorProfile::STATUS_PENDING) {
                throw new \Exception('Only pending profiles can be rejected');
            }

            $profile->reject(auth('admin')->id(), $reason);

            // Notify the clinic user
            $profile->clinicUser->notify(new ProfileRejected($profile));

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

    private function formatSpecialties($item): string
    {
        if ($item->specialties && is_array($item->specialties)) {
            $badges = collect($item->specialties)->map(function ($specialty) {
                return '<span class="badge bg-primary me-1 mb-1">' . e($specialty) . '</span>';
            })->implode(' ');

            return '<div class="d-flex flex-wrap">' . $badges . '</div>';
        }

        return '<span class="badge bg-secondary">No Specialties</span>';
    }

    private function profileActions($item): string
    {
        $showUrl = route('admin.doctor-profiles.show', $item->id);
        $actions = '<div class="d-flex gap-2">';

        $actions .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';

        if ($item->status === DoctorProfile::STATUS_PENDING) {
            $actions .= '<button onclick="approveProfile(' . $item->id . ')" class="btn btn-sm btn-success" title="Approve"><i class="fa fa-check"></i></button>';
            $actions .= '<button onclick="rejectProfile(' . $item->id . ')" class="btn btn-sm btn-danger" title="Reject"><i class="fa fa-times"></i></button>';
        }

        $actions .= '</div>';
        return $actions;
    }

    private function pendingActions($item): string
    {
        $showUrl = route('admin.doctor-profiles.show', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
            <a href="{$showUrl}" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i></a>
            <button onclick="approveProfile({$item->id})" class="btn btn-sm btn-success" title="Approve"><i class="fa fa-check"></i></button>
            <button onclick="rejectProfile({$item->id})" class="btn btn-sm btn-danger" title="Reject"><i class="fa fa-times"></i></button>
        </div>
        HTML;
    }
}
