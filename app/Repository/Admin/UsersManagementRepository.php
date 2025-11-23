<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\UsersManagementRepositoryInterface;
use App\Models\Clinic;
use App\Models\ClinicUser;
use App\Models\Patient;
use App\Models\DoctorProfile;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Illuminate\Support\Facades\DB;

class UsersManagementRepository implements UsersManagementRepositoryInterface
{
    public function getOverviewStats()
    {
        return [
            'clinics' => [
                'total' => Clinic::count(),
                'active' => Clinic::where('status', true)->count(),
                'inactive' => Clinic::where('status', false)->count(),
            ],
            'patients' => [
                'total' => Patient::count(),
                'active' => Patient::count(), // All patients are active (registered users)
                'inactive' => 0, // No inactive patients
            ],
            'doctor_profiles' => [
                'total' => DoctorProfile::count(),
                'approved' => DoctorProfile::where('status', 'approved')->count(),
                'pending' => DoctorProfile::where('status', 'pending')->count(),
                'standalone' => DoctorProfile::whereHas('clinicUser', function($q) {
                    $q->whereNull('clinic_id');
                })->count(),
                'clinic_based' => DoctorProfile::whereHas('clinicUser', function($q) {
                    $q->whereNotNull('clinic_id');
                })->count(),
            ],
            'suppliers' => [
                'total' => Supplier::count(),
                'active' => Supplier::where('status', true)->count(),
                'inactive' => Supplier::where('status', false)->count(),
            ],
            'clinic_users' => [
                'total' => ClinicUser::count(),
            ],
            'supplier_users' => [
                'total' => SupplierUser::count(),
            ],
        ];
    }

    public function getClinicsData()
    {
        $clinics = Clinic::withCount(['clinicUsers', 'doctorProfiles'])->get();

        return datatables()->of($clinics)
            ->addColumn('admin_name', function($item) {
                $admin = $item->clinicUsers()->where('has_clinic', true)->first();
                return $admin ? $admin->name : 'N/A';
            })
            ->addColumn('admin_email', function($item) {
                $admin = $item->clinicUsers()->where('has_clinic', true)->first();
                return $admin ? $admin->email : 'N/A';
            })
            ->addColumn('users_count', fn($item) => $item->clinic_users_count)

            ->editColumn('status', fn($item) => $item->status
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('action', function($item) {
                $viewUrl = route('admin.users-management.clinic-details', $item->id);
                return '<a href="'.$viewUrl.'" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i> Details</a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function getClinicUsersData()
    {
        $clinicUsers = ClinicUser::with('clinic')
            ->whereNotNull('clinic_id')
            ->get();

        return datatables()->of($clinicUsers)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('phone', fn($item) => $item->phone ?? 'N/A')
            ->addColumn('clinic_name', fn($item) => $item->clinic ? $item->clinic->name : 'N/A')
            ->addColumn('role', function($item) {
                if ($item->has_clinic) {
                    return '<span class="badge bg-primary">Admin</span>';
                } elseif ($item->doctorProfile) {
                    return '<span class="badge bg-info">Doctor</span>';
                } else {
                    return '<span class="badge bg-secondary">Staff</span>';
                }
            })
            ->editColumn('status', fn($item) => $item->status
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('action', function($item) {
                $viewUrl = route('admin.users-management.clinic-user-details', $item->id);
                return '<a href="'.$viewUrl.'" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i> Details</a>';
            })
            ->rawColumns(['role', 'status', 'action'])
            ->make(true);
    }

    public function getPatientsData()
    {
        $patients = Patient::with(['governorate', 'city'])
            ->withCount('doctors')
            ->get();

        return datatables()->of($patients)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('phone', fn($item) => $item->phone ?? 'N/A')
            ->addColumn('location', function($item) {
                $parts = [];
                if ($item->governorate) $parts[] = $item->governorate->name_en;
                if ($item->city) $parts[] = $item->city->name_en;
                return implode(', ', $parts) ?: 'N/A';
            })
            ->addColumn('doctors_count', fn($item) => $item->doctors_count)
            ->addColumn('action', function($item) {
                $viewUrl = route('admin.users-management.patient-details', $item->id);
                return '<a href="'.$viewUrl.'" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i> Details</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getDoctorProfilesData()
    {
        $doctorProfiles = DoctorProfile::with(['clinicUser.clinic', 'speciality'])
            ->withCount('patients')
            ->get();

        return datatables()->of($doctorProfiles)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('phone', fn($item) => $item->phone ?? 'N/A')
            ->addColumn('speciality', fn($item) => $item->speciality?->name_en ?? 'N/A')
            ->addColumn('clinic', function($item) {
                if ($item->clinicUser && $item->clinicUser->clinic_id) {
                    return $item->clinicUser->clinic->name ?? 'N/A';
                }
                return '<span class="badge bg-warning">Standalone</span>';
            })
            ->addColumn('patients_count', fn($item) => $item->patients_count)

            ->addColumn('approval_status', function($item) {
                if ($item->status === 'approved') {
                    return '<span class="badge bg-success">Approved</span>';
                } elseif ($item->status === 'rejected') {
                    return '<span class="badge bg-danger">Rejected</span>';
                } else {
                    return '<span class="badge bg-warning">Pending</span>';
                }
            })

            ->addColumn('action', function($item) {
                $viewUrl = route('admin.users-management.doctor-profile-details', $item->id);
                return '<a href="'.$viewUrl.'" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Details</a>';
            })

            ->rawColumns(['clinic', 'status', 'approval_status', 'action'])
            ->make(true);
    }

    public function getSuppliersData()
    {
        $suppliers = Supplier::withCount('supplierUsers')->get();

        return datatables()->of($suppliers)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('phone', fn($item) => $item->phone ?? 'N/A')
            ->addColumn('users_count', fn($item) => $item->supplier_users_count)
            ->editColumn('status', fn($item) => $item->status
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('action', function($item) {
                $viewUrl = route('admin.users-management.supplier-details', $item->id);
                return '<a href="'.$viewUrl.'" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i> Details</a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function getClinicDetails($clinicId)
    {
        return Clinic::with([
            'clinicUsers' => function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'governorate',
            'city'
        ])->findOrFail($clinicId);
    }

    public function getClinicUserDetails($clinicUserId)
    {
        return ClinicUser::with([
            'clinic',
            'doctorProfile.speciality',
            'doctorProfile.patients'
        ])->findOrFail($clinicUserId);
    }

    public function getPatientDetails($patientId)
    {
        return Patient::with([
            'governorate',
            'city',
            'doctors' => function($q) {
                $q->withPivot(['clinic_id', 'assigned_at', 'assigned_by'])
                  ->with(['clinic', 'speciality']);
            }
        ])->findOrFail($patientId);
    }

    public function getDoctorProfileDetails($doctorProfileId)
    {
        return DoctorProfile::with([
            'clinicUser.clinic',
            'speciality',
            'patients' => function($q) {
                $q->withPivot(['clinic_id', 'assigned_at', 'assigned_by'])
                  ->with('clinic');
            }
        ])->findOrFail($doctorProfileId);
    }

    public function getSupplierDetails($supplierId)
    {
        return Supplier::with([
            'supplierUsers' => function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'governorate',
            'city'
        ])->findOrFail($supplierId);
    }
}
