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
    public function getSupplierUsersTrashData();
    public function destroySupplierUser($id);
    public function restoreSupplierUser($id);
    public function forceDeleteSupplierUser($id);

    // Patients Trash
    public function getPatientsTrashData();
    public function destroyPatient($id);
    public function restorePatient($id);
    public function forceDeletePatient($id);

    // Clinic Users Trash
    public function getClinicUsersTrashData();
    public function destroyClinicUser($id);
    public function restoreClinicUser($id);
    public function forceDeleteClinicUser($id);
}
