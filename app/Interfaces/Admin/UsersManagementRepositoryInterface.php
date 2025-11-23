<?php

namespace App\Interfaces\Admin;

interface UsersManagementRepositoryInterface
{
    public function getOverviewStats();
    public function getClinicsData();
    public function getClinicUsersData();
    public function getPatientsData();
    public function getDoctorProfilesData();
    public function getSuppliersData();
    public function getSupplierUsersData();
    public function getClinicDetails($clinicId);
    public function getClinicUserDetails($clinicUserId);
    public function getPatientDetails($patientId);
    public function getDoctorProfileDetails($doctorProfileId);
    public function getSupplierDetails($supplierId);
    public function getSupplierUserDetails($supplierUserId);
}
