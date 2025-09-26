<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\DoctorProfileRepositoryInterface;
use Illuminate\Http\Request;

class DoctorProfileController extends Controller
{
    protected $profileRepo;

    public function __construct(DoctorProfileRepositoryInterface $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.doctor-profiles.index');
    }

    public function data()
    {
        return $this->profileRepo->data();
    }

    public function pending()
    {
        return view('backend.dashboards.admin.pages.doctor-profiles.pending');
    }

    public function pendingData()
    {
        return $this->profileRepo->pendingData();
    }

    public function show($id)
    {
        $profile = $this->profileRepo->show($id);
        return view('backend.dashboards.admin.pages.doctor-profiles.show', compact('profile'));
    }

    public function approve($id)
    {
        try {
            $this->profileRepo->approve($id);
            return $this->jsonResponse('success', __('Profile approved successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        try {
            $this->profileRepo->reject($id, $request->rejection_reason);
            return $this->jsonResponse('success', __('Profile rejected successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function toggleFeatured($id)
    {
        try {
            $profile = $this->profileRepo->toggleFeatured($id);
            $message = $profile->is_featured
                ? __('Profile marked as featured successfully')
                : __('Profile removed from featured successfully');

            return $this->jsonResponse('success', $message);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function toggleLockForEdit($id)
    {
        try {
            $profile = $this->profileRepo->toggleLockForEdit($id);
            $message = $profile->locked_for_edit
                ? __('Profile locked for editing successfully')
                : __('Profile unlocked for editing successfully');

            return $this->jsonResponse('success', $message);
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
