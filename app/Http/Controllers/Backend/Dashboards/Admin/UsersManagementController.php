<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\UsersManagementRepositoryInterface;

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
        $stats = $this->repo->getOverviewStats();
        return view('backend.dashboards.admin.pages.users-management.index', compact('stats'));
    }

    // Clinic Users
    public function clinics()
    {
        return view('backend.dashboards.admin.pages.users-management.clinics');
    }

    public function clinicsData()
    {
        return $this->repo->getClinicsData();
    }

    public function clinicDetails($id)
    {
        $clinic = $this->repo->getClinicDetails($id);
        return view('backend.dashboards.admin.pages.users-management.clinic-details', compact('clinic'));
    }

    // Clinic Users
    public function clinicUsers()
    {
        return view('backend.dashboards.admin.pages.users-management.clinic-users');
    }

    public function clinicUsersData()
    {
        return $this->repo->getClinicUsersData();
    }

    public function clinicUserDetails($id)
    {
        $clinicUser = $this->repo->getClinicUserDetails($id);
        return view('backend.dashboards.admin.pages.users-management.clinic-user-details', compact('clinicUser'));
    }

    // Patients
    public function patients()
    {
        return view('backend.dashboards.admin.pages.users-management.patients');
    }

    public function patientsData()
    {
        return $this->repo->getPatientsData();
    }

    public function patientDetails($id)
    {
        $patient = $this->repo->getPatientDetails($id);
        return view('backend.dashboards.admin.pages.users-management.patient-details', compact('patient'));
    }

    // Doctor Profiles
    public function doctorProfiles()
    {
        return view('backend.dashboards.admin.pages.users-management.doctor-profiles');
    }

    public function doctorProfilesData()
    {
        return $this->repo->getDoctorProfilesData();
    }

    public function doctorProfileDetails($id)
    {
        $doctorProfile = $this->repo->getDoctorProfileDetails($id);
        return view('backend.dashboards.admin.pages.users-management.doctor-profile-details', compact('doctorProfile'));
    }

    // Suppliers
    public function suppliers()
    {
        return view('backend.dashboards.admin.pages.users-management.suppliers');
    }

    public function suppliersData()
    {
        return $this->repo->getSuppliersData();
    }

    public function supplierDetails($id)
    {
        $supplier = $this->repo->getSupplierDetails($id);
        return view('backend.dashboards.admin.pages.users-management.supplier-details', compact('supplier'));
    }
}
