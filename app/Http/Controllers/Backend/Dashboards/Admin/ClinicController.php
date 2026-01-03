<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\ClinicRepositoryInterface;
use App\Http\Requests\Admin\Store\StoreClinicRequest;
use App\Http\Requests\Admin\Update\UpdateClinicRequest;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class ClinicController extends Controller
{


    protected $clinicRepo;

    public function __construct(ClinicRepositoryInterface $clinicRepo)
    {
        $this->clinicRepo = $clinicRepo;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.clinics.index');
    }

    public function data()
    {
        return $this->clinicRepo->data();
    }

    public function clinicUsersData($id)
    {
        return $this->clinicRepo->clinicUsersData($id);
    }

    public function store(StoreClinicRequest $request)
    {
        return $this->clinicRepo->store($request);
    }

    public function show($id)
    {
        $clinic = $this->clinicRepo->show($id);

        return request()->ajax()
            ? response()->json($clinic)
            : view('backend.dashboards.admin.pages.clinics.show', compact('clinic'));
    }

    public function update(UpdateClinicRequest $request, $id)
    {
        return $this->clinicRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->clinicRepo->updateStatus($request);
    }

    public function updateIsAllowed(Request $request)
    {
        return $this->clinicRepo->updateIsAllowed($request);
    }

    public function toggleStatus($id)
    {
        $clinic = Clinic::findOrFail($id);
        $clinic->update(['status' => !$clinic->status]);

        return $this->jsonResponse('success', __('Clinic status updated successfully'));
    }

    public function destroy($id)
    {
        return $this->clinicRepo->destroy($id);
    }

    public function showApproval($id)
    {
        return $this->clinicRepo->showApproval($id);
    }

    /**
     * Toggle the rental space company flag for a clinic.
     * When enabled, users are assigned the 'rental-space-manager' role.
     * When disabled, users are restored to the 'clinic-admin' role.
     */
    public function toggleRentalSpaceCompany($id)
    {
        $clinic = Clinic::findOrFail($id);
        $newStatus = !$clinic->is_rental_space_company;
        $clinic->update(['is_rental_space_company' => $newStatus]);

        // Update roles for all clinic users
        $teamId = $clinic->id;
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($teamId);
        }

        $targetRoleName = $newStatus ? 'rental-space-manager' : 'clinic-admin';

        // Get or create the target role for this clinic
        $role = Role::firstOrCreate([
            'name' => $targetRoleName,
            'guard_name' => 'clinic',
            'team_id' => $teamId,
        ]);

        // Define the permissions for each role
        if ($newStatus) {
            // Rental space manager permissions (limited)
            $permissions = [
                'view dashboard',
                'view rental spaces',
                'create rental space',
                'update rental space',
                'delete rental space',
                'view trash rental spaces',
                'restore rental space',
                'force delete rental space',
                'toggle rental space status',
                'view subscriptions',
                'view subscription usage',
                'view notifications',
                'mark notification as read',
                'mark all notifications as read',
            ];
        } else {
            // Clinic admin has all permissions
            $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'clinic')
                ->pluck('name')
                ->toArray();
        }

        // Sync permissions to the role
        $role->syncPermissions($permissions);

        // Sync all clinic users to the new role
        foreach ($clinic->clinicUsers as $user) {
            $user->syncRoles([$role]);
        }

        $message = $newStatus
            ? __('Clinic marked as Rental Space Company. Users now have limited access.')
            : __('Clinic unmarked as Rental Space Company. Users restored to full access.');

        return $this->jsonResponse('success', $message);
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message], $status === 'error' ? 400 : 200);
        }

        return redirect()->back()->with($status, $message);
    }
}
