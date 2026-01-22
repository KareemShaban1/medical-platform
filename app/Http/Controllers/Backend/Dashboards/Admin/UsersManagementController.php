<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\UsersManagementRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UsersManagementController extends Controller
{
    protected $repo;

    public function __construct(UsersManagementRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    // Overview page with analytics
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view system management'), 403, __('You are not authorized to view overview stats'));
        $stats = $this->repo->getOverviewStats();
        return view('backend.dashboards.admin.pages.users-management.index', compact('stats'));
    }

    // Clinic Users
    public function clinics()
    {
        // apply permissions
        abort_if(!hasPermission('view system clinics'), 403, __('You are not authorized to view clinics'));
        return view('backend.dashboards.admin.pages.users-management.clinics');
    }

    public function clinicsData()
    {
        // apply permissions
        abort_if(!hasPermission('view system clinics'), 403, __('You are not authorized to view clinics'));
        return $this->repo->getClinicsData();
    }

    public function clinicDetails($id)
    {
        // apply permissions
        abort_if(!hasPermission('view system clinics'), 403, __('You are not authorized to view clinics'));
        $clinic = $this->repo->getClinicDetails($id);
        return view('backend.dashboards.admin.pages.users-management.clinic-details', compact('clinic'));
    }

    // Clinic Users
    public function clinicUsers()
    {
        // apply permissions
        abort_if(!hasPermission('view system clinic users'), 403, __('You are not authorized to view clinic users'));

        return view('backend.dashboards.admin.pages.users-management.clinic-users');
    }

    public function clinicUsersData()
    {
        // apply permissions
        abort_if(!hasPermission('view system clinic users'), 403, __('You are not authorized to view clinic users'));
        return $this->repo->getClinicUsersData();
    }

    public function clinicUserDetails($id)
    {
        // apply permissions
        abort_if(!hasPermission('view system clinic users'), 403, __('You are not authorized to view clinic users'));
        $clinicUser = $this->repo->getClinicUserDetails($id);
        return view('backend.dashboards.admin.pages.users-management.clinic-user-details', compact('clinicUser'));
    }

    // Patients
    public function patients()
    {
        // apply permissions
        abort_if(!hasPermission('view patients'), 403, __('You are not authorized to view patients'));
        return view('backend.dashboards.admin.pages.users-management.patients');
    }

    public function patientsData()
    {
        // apply permissions
        abort_if(!hasPermission('view patients'), 403, __('You are not authorized to view patients'));
        return $this->repo->getPatientsData();
    }

    public function patientDetails($id)
    {
        // apply permissions
        abort_if(!hasPermission('view patients'), 403, __('You are not authorized to view patients'));
        $patient = $this->repo->getPatientDetails($id);
        return view('backend.dashboards.admin.pages.users-management.patient-details', compact('patient'));
    }

    public function patientsTrash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash patients'), 403, __('You are not authorized to view trash'));
        return view('backend.dashboards.admin.pages.users-management.patients-trash');
    }

    public function patientsTrashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash patients'), 403, __('You are not authorized to view trash'));
        return $this->repo->getPatientsTrashData();
    }

    public function destroyPatient($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete patient'), 403, __('You are not authorized to delete patient'));
        return $this->repo->destroyPatient($id);
    }

    public function restorePatient($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore patient'), 403, __('You are not authorized to restore patient'));
        return $this->repo->restorePatient($id);
    }

    public function forceDeletePatient($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete patient'), 403, __('You are not authorized to force delete patient'));
        return $this->repo->forceDeletePatient($id);
    }

    // Doctor Profiles
    public function doctorProfiles()
    {
        // apply permissions
        abort_if(!hasPermission('view system doctor profiles'), 403, __('You are not authorized to view doctor profiles'));
        return view('backend.dashboards.admin.pages.users-management.doctor-profiles');
    }

    public function doctorProfilesData()
    {
        // apply permissions
        abort_if(!hasPermission('view system doctor profiles'), 403, __('You are not authorized to view doctor profiles'));
        return $this->repo->getDoctorProfilesData();
    }

    public function doctorProfileDetails($id)
    {
        // apply permissions
        abort_if(!hasPermission('view system doctor profiles'), 403, __('You are not authorized to view doctor profiles'));
        $doctor = $this->repo->getDoctorProfileDetails($id);
        return view('backend.dashboards.admin.pages.users-management.doctor-profile-details', compact('doctor'));
    }

    // Suppliers
    public function suppliers()
    {
        // apply permissions
        abort_if(!hasPermission('view system suppliers'), 403, __('You are not authorized to view suppliers'));
        return view('backend.dashboards.admin.pages.users-management.suppliers');
    }

    public function suppliersData()
    {
        // apply permissions
        abort_if(!hasPermission('view system suppliers'), 403, __('You are not authorized to view suppliers'));
        return $this->repo->getSuppliersData();
    }

    public function supplierDetails($id)
    {
        // apply permissions
        abort_if(!hasPermission('view system suppliers'), 403, __('You are not authorized to view suppliers'));
        $supplier = $this->repo->getSupplierDetails($id);
        return view('backend.dashboards.admin.pages.users-management.supplier-details', compact('supplier'));
    }

    // Supplier Users
    public function supplierUsers()
    {
        // apply permissions
        abort_if(!hasPermission('view system supplier users'), 403, __('You are not authorized to view supplier users'));
        return view('backend.dashboards.admin.pages.users-management.supplier-users');
    }

    public function supplierUsersData()
    {
        // apply permissions
        abort_if(!hasPermission('view system supplier users'), 403, __('You are not authorized to view supplier users'));
        return $this->repo->getSupplierUsersData();
    }

    public function supplierUserDetails($id)
    {
        // apply permissions
        abort_if(!hasPermission('view system supplier users'), 403, __('You are not authorized to view supplier users'));
        $supplierUser = $this->repo->getSupplierUserDetails($id);
        return view('backend.dashboards.admin.pages.users-management.supplier-user-details', compact('supplierUser'));
    }

    public function supplierUsersTrash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash users'), 403, __('You are not authorized to view trash users'));
        return view('backend.dashboards.admin.pages.users-management.supplier-users-trash');
    }

    public function supplierUsersTrashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash users'), 403, __('You are not authorized to view trash users'));
        return $this->repo->getSupplierUsersTrashData();
    }

    public function destroySupplierUser($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete user'), 403, __('You are not authorized to delete user'));
        return $this->repo->destroySupplierUser($id);
    }

    public function restoreSupplierUser($id)
    {
        // apply permissions
        // using delete user as restore permission if no specific restore available, or assuming admins have broad access
        // checking seeder: 'restore admin user' exists, but generic 'restore user' doesn't.
        // using 'update user' or 'delete user' logic. Let's stick with 'delete user' for consistency with soft deletes
        abort_if(!hasPermission('delete user'), 403, __('You are not authorized to restore user'));
        return $this->repo->restoreSupplierUser($id);
    }

    public function forceDeleteSupplierUser($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete user'), 403, __('You are not authorized to force delete user'));
        return $this->repo->forceDeleteSupplierUser($id);
    }

    // Clinic Users Trash
    public function clinicUsersTrash()
    {
        abort_if(!hasPermission('view trash users'), 403, __('You are not authorized to view trash'));
        return view('backend.dashboards.admin.pages.users-management.clinic-users-trash');
    }

    public function clinicUsersTrashData()
    {
        abort_if(!hasPermission('view trash users'), 403, __('You are not authorized to view trash'));
        return $this->repo->getClinicUsersTrashData();
    }

    public function destroyClinicUser($id)
    {
        abort_if(!hasPermission('delete user'), 403, __('You are not authorized to delete'));
        return $this->repo->destroyClinicUser($id);
    }

    public function restoreClinicUser($id)
    {
        abort_if(!hasPermission('restore user'), 403, __('You are not authorized to restore'));
        return $this->repo->restoreClinicUser($id);
    }

    public function forceDeleteClinicUser($id)
    {
        abort_if(!hasPermission('force delete user'), 403, __('You are not authorized to force delete'));
        return $this->repo->forceDeleteClinicUser($id);
    }

    // Password Management
    public function changePassword(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'user_type' => 'required|in:clinic_user,supplier_user,user,doctor_profile',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        try {
            $userType = $request->user_type;
            $userId = $request->user_id;

            // Get the user based on type and update password
            if ($userType === 'clinic_user') {
                // Clinic User - password stored directly in clinic_users table
                $user = \App\Models\ClinicUser::findOrFail($userId);
                $user->password = Hash::make($request->new_password);
                $user->save();
            } elseif ($userType === 'supplier_user') {
                // Supplier User - password stored directly in supplier_users table
                $user = \App\Models\SupplierUser::findOrFail($userId);
                $user->password = Hash::make($request->new_password);
                $user->save();
            } elseif ($userType === 'doctor_profile') {
                // Doctor Profile - password stored in clinic_users table via clinic_user_id
                $doctorProfile = \App\Models\DoctorProfile::findOrFail($userId);
                if (!$doctorProfile->clinic_user_id) {
                    throw new \Exception(__('Doctor profile is not linked to a clinic user account'));
                }

                $clinicUser = \App\Models\ClinicUser::findOrFail($doctorProfile->clinic_user_id);
                $clinicUser->password = Hash::make($request->new_password);
                $clinicUser->save();
            } else {
                // Patient - password stored in users table via user_id
                $patient = \App\Models\Patient::findOrFail($userId);
                if (!$patient->user_id) {
                    throw new \Exception(__('Patient is not linked to a user account'));
                }

                $user = \App\Models\User::findOrFail($patient->user_id);
                $user->password = Hash::make($request->new_password);
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => __('Password changed successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to change password: ') . $e->getMessage(),
            ], 500);
        }
    }

    // Toggle Account Status
    public function toggleStatus(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'user_type' => 'required|in:clinic_user,supplier_user,user',
            'status' => 'required|in:0,1',
        ]);

        try {
            $userType = $request->user_type;
            $userId = $request->user_id;
            $newStatus = (int) $request->status;
            $subject = __('Account');

            // Get the user based on type
            if ($userType === 'clinic_user') {
                $user = \App\Models\ClinicUser::findOrFail($userId);
                $subject = __('Clinic User');
            } elseif ($userType === 'supplier_user') {
                $user = \App\Models\SupplierUser::findOrFail($userId);
                $subject = __('Supplier User');
            } elseif ($userType === 'user') {
                // Assuming 'user' refers to the main User model for patients/others
                $user = \App\Models\User::findOrFail($userId);
                $subject = __('User');
            }

            if (isset($user)) {
                // Update status
                $user->status = $newStatus;
                $user->save();
            }

            $statusText = $newStatus ? __('activated') : __('deactivated');

            return response()->json([
                'success' => true,
                'message' => $subject . ' ' . $statusText . ' ' . __('successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update account status: ') . $e->getMessage(),
            ], 500);
        }
    }
}
