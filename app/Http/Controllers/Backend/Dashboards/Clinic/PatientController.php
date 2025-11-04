<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\PatientRepositoryInterface;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $patientRepo;

    public function __construct(PatientRepositoryInterface $patientRepo)
    {
        $this->patientRepo = $patientRepo;
    }

    public function index()
    {
        $clinicUser = auth('clinic')->user();
        $isDoctor = $clinicUser->isDoctor();

        // Get all doctors in the clinic for the dropdown (only if user is not a doctor)
        $doctors = [];
        if (!$isDoctor) {
            $doctors = \App\Models\DoctorProfile::whereHas('clinicUser', function ($query) use ($clinicUser) {
                $query->where('clinic_id', $clinicUser->clinic_id);
            })->get(['id', 'name']);
        }

        return view('backend.dashboards.clinic.pages.patients.index', compact('isDoctor', 'doctors'));
    }

    public function data()
    {
        return $this->patientRepo->data();
    }

    public function store(Request $request)
    {
        $clinicUser = auth('clinic')->user();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:patients,phone',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8',
        ];

        // If user is not a doctor, they can optionally assign to a doctor
        if (!$clinicUser->isDoctor()) {
            $rules['doctor_profile_id'] = 'nullable|exists:doctor_profiles,id';
        }

        $request->validate($rules);

        try {
            $this->patientRepo->store($request->all());
            return $this->jsonResponse('success', __('Patient created/assigned successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $clinicUser = auth('clinic')->user();
        $patient = $this->patientRepo->show($id);

        // Load all medical data for this patient in this clinic
        $appointments = \App\Models\Appointment::where('patient_id', $id)
            ->whereHas('doctorProfile.clinicUser', function($q) use ($clinicUser) {
                $q->where('clinic_id', $clinicUser->clinic_id);
            })
            ->with(['doctorProfile', 'prescription.items'])
            ->orderBy('created_at', 'desc')
            ->get();

        $medicalRecords = \App\Models\MedicalRecord::where('patient_id', $id)
            ->where('clinic_id', $clinicUser->clinic_id)
            ->with(['doctor', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $prescriptions = \App\Models\Prescription::where('patient_id', $id)
            ->where('clinic_id', $clinicUser->clinic_id)
            ->with(['doctorProfile', 'items', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $labOrders = \App\Models\LabOrder::where('patient_id', $id)
            ->where('clinic_id', $clinicUser->clinic_id)
            ->with(['doctorProfile', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('status', 'completed')->count(),
            'total_prescriptions' => $prescriptions->count(),
            'total_lab_orders' => $labOrders->count(),
            'total_medical_records' => $medicalRecords->count(),
        ];

        // Get assigned doctors for this patient in this clinic
        $assignedDoctors = $patient->doctors()
            ->wherePivot('clinic_id', $clinicUser->clinic_id)
            ->get();

        return view('backend.dashboards.clinic.pages.patients.show', compact(
            'patient',
            'appointments',
            'medicalRecords',
            'prescriptions',
            'labOrders',
            'stats',
            'assignedDoctors'
        ));
    }

    public function edit(Request $request, $id)
    {
        $patient = $this->patientRepo->edit($request, $id);
        return $patient;
    }

    public function update(Request $request, $id)
    {
        $clinicUser = auth('clinic')->user();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:patients,phone,' . $id,
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8',
        ];

        // If user is not a doctor, they can optionally assign to a doctor
        if (!$clinicUser->isDoctor()) {
            $rules['doctor_profile_id'] = 'nullable|exists:doctor_profiles,id';
        }

        $request->validate($rules);

        try {
            $this->patientRepo->update($request->all(), $id);
            return $this->jsonResponse('success', __('Patient updated successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->patientRepo->destroy($id);
            return $this->jsonResponse('success', __('Patient deleted successfully'));
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