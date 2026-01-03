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
        // apply permissions
        abort_if(!hasPermission('view doctor profiles'), 403, __('You are not authorized to view doctor profiles'));
        return view('backend.dashboards.admin.pages.doctor-profiles.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view doctor profiles'), 403, __('You are not authorized to view doctor profiles'));
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
        // apply permissions
        abort_if(!hasPermission('view doctor profiles'), 403, __('You are not authorized to view doctor profile'));
        $profile = $this->profileRepo->show($id);
        return view('backend.dashboards.admin.pages.doctor-profiles.show', compact('profile'));
    }

    public function approve($id)
    {
        // apply permissions
        abort_if(!hasPermission('approve doctor profile'), 403, __('You are not authorized to approve doctor profile'));
        try {
            $this->profileRepo->approve($id);
            return $this->jsonResponse('success', __('Profile approved successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('reject doctor profile'), 403, __('You are not authorized to reject doctor profile'));
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
        // apply permissions
        abort_if(!hasPermission('toggle featured doctor profile'), 403, __('You are not authorized to toggle featured doctor profile'));
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
        // apply permissions
        abort_if(!hasPermission('toggle lock doctor profile'), 403, __('You are not authorized to toggle lock for edit doctor profile'));
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

    public function trash()
    {
        abort_if(!hasPermission('view trash doctor profiles'), 403, __('You are not authorized to view trash'));
        return view('backend.dashboards.admin.pages.doctor-profiles.trash');
    }

    public function trashData()
    {
        abort_if(!hasPermission('view trash doctor profiles'), 403, __('You are not authorized to view trash'));
        return $this->profileRepo->trashData();
    }

    public function destroy($id)
    {
        abort_if(!hasPermission('delete doctor profile'), 403, __('You are not authorized to delete'));
        return $this->profileRepo->destroy($id);
    }

    public function restore($id)
    {
        abort_if(!hasPermission('restore doctor profile'), 403, __('You are not authorized to restore'));
        return $this->profileRepo->restore($id);
    }

    public function forceDelete($id)
    {
        abort_if(!hasPermission('force delete doctor profile'), 403, __('You are not authorized to force delete'));
        return $this->profileRepo->forceDelete($id);
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
