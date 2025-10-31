<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\PatientRepositoryInterface;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientRepository implements PatientRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $clinicUser = auth('clinic')->user();

        // Check if the logged-in user is a doctor
        if ($clinicUser->isDoctor()) {
            // Show only patients assigned to this doctor in this clinic
            $doctorProfile = $clinicUser->getDoctorProfile();
            $patients = Patient::with(['user', 'doctors'])
                ->forDoctorInClinic($doctorProfile->id, $clinicUser->clinic_id);
        } else {
            // Show all patients in the clinic
            $patients = Patient::with(['user', 'doctors'])
                ->forClinic($clinicUser->clinic_id);
        }

        return datatables()->of($patients)
            ->addColumn('name', fn($item) => $item->user ? $item->user->name : 'N/A')
            ->addColumn('phone', fn($item) => $item->phone)
            ->addColumn('email', fn($item) => $item->user ? $item->user->email : 'N/A')
            ->addColumn('status', fn($item) => $item->status_badge)
            ->addColumn('type', fn($item) => $this->getPatientType($item))
            ->addColumn('assigned_doctors', fn($item) => $this->getAssignedDoctorsForClinic($item, $clinicUser->clinic_id))
            ->addColumn('action', fn($item) => $this->patientActions($item))
            ->rawColumns(['status', 'type', 'assigned_doctors', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;
            $clinicUser = auth('clinic')->user();

            // First, create or find the user
            $user = \App\Models\User::where('email', $data['email'])->first();

            if (!$user) {
                // Create new user
                $user = \App\Models\User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'] ?? 'defaultpassword123'),
                ]);
            }

            // Create or find patient by user_id or phone
            $patient = Patient::where('user_id', $user->id)->first();

            if (!$patient) {
                // Check if phone already exists globally
                $existingPatient = Patient::where('phone', $data['phone'])->first();
                if ($existingPatient) {
                    throw new \Exception('Phone number is already registered to another patient.');
                }

                // Create patient record linked to user
                $patient = Patient::create([
                    'user_id' => $user->id,
                    'phone' => $data['phone'],
                ]);
            }

            // Check if patient is already assigned to a doctor in this clinic
            $existingAssignment = $patient->doctors()
                ->wherePivot('clinic_id', $clinicUser->clinic_id)
                ->exists();

            // If the logged-in user is a doctor, automatically assign the patient to them
            if ($clinicUser->isDoctor()) {
                $doctorProfile = $clinicUser->getDoctorProfile();

                // Check if already assigned to this doctor in this clinic
                $alreadyAssigned = $patient->doctors()
                    ->wherePivot('doctor_profile_id', $doctorProfile->id)
                    ->wherePivot('clinic_id', $clinicUser->clinic_id)
                    ->exists();

                if (!$alreadyAssigned) {
                    $patient->doctors()->attach($doctorProfile->id, [
                        'clinic_id' => $clinicUser->clinic_id,
                        'assigned_by' => $clinicUser->id,
                        'assigned_at' => now(),
                    ]);
                }
            } elseif (isset($data['doctor_profile_id']) && !empty($data['doctor_profile_id'])) {
                // If clinic staff is creating and selects a doctor, assign to that doctor
                $alreadyAssigned = $patient->doctors()
                    ->wherePivot('doctor_profile_id', $data['doctor_profile_id'])
                    ->wherePivot('clinic_id', $clinicUser->clinic_id)
                    ->exists();

                if (!$alreadyAssigned) {
                    $patient->doctors()->attach($data['doctor_profile_id'], [
                        'clinic_id' => $clinicUser->clinic_id,
                        'assigned_by' => $clinicUser->id,
                        'assigned_at' => now(),
                    ]);
                }
            }

            return $patient;
        });
    }

    public function show($id)
    {
        $clinicUser = auth('clinic')->user();
        return Patient::with(['user', 'doctors'])
            ->forClinic($clinicUser->clinic_id)
            ->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $clinicUser = auth('clinic')->user();
            $patient = Patient::with('user')->forClinic($clinicUser->clinic_id)->findOrFail($id);
            $data = $request;

            // Update the linked user information (name, email, password)
            if ($patient->user) {
                $userUpdateData = [];

                if (!empty($data['name'])) {
                    $userUpdateData['name'] = $data['name'];
                }

                if (!empty($data['email']) && $data['email'] !== $patient->user->email) {
                    // Check if email is already used by another user
                    $existingUser = \App\Models\User::where('email', $data['email'])
                        ->where('id', '!=', $patient->user->id)
                        ->first();
                    if ($existingUser) {
                        throw new \Exception('Email is already used by another user.');
                    }
                    $userUpdateData['email'] = $data['email'];
                }

                if (!empty($data['password'])) {
                    $userUpdateData['password'] = Hash::make($data['password']);
                }

                if (!empty($userUpdateData)) {
                    $patient->user->update($userUpdateData);
                }
            }

            // Update patient record (phone is stored here)
            $patientUpdateData = [];
            if (!empty($data['phone']) && $data['phone'] !== $patient->phone) {
                // Check if phone is already used by another patient
                $existingPatient = Patient::where('phone', $data['phone'])
                    ->where('id', '!=', $patient->id)
                    ->first();

                if ($existingPatient) {
                    throw new \Exception('Phone number is already registered to another patient.');
                }

                $patientUpdateData['phone'] = $data['phone'];
            }

            if (!empty($patientUpdateData)) {
                $patient->update($patientUpdateData);
            }

            // Handle doctor assignment updates (clinic staff only)
            if (!$clinicUser->isDoctor() && isset($data['doctor_profile_id'])) {
                if (!empty($data['doctor_profile_id'])) {
                    // Check if already assigned to this doctor in this clinic
                    $alreadyAssigned = $patient->doctors()
                        ->wherePivot('doctor_profile_id', $data['doctor_profile_id'])
                        ->wherePivot('clinic_id', $clinicUser->clinic_id)
                        ->exists();

                    if (!$alreadyAssigned) {
                        $patient->doctors()->attach($data['doctor_profile_id'], [
                            'clinic_id' => $clinicUser->clinic_id,
                            'assigned_by' => $clinicUser->id,
                            'assigned_at' => now(),
                        ]);
                    }
                }
            }

            return $patient->refresh();
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $clinicUser = auth('clinic')->user();
            $patient = Patient::forClinic($clinicUser->clinic_id)->findOrFail($id);

            // Only remove the assignment in this clinic, don't delete the patient
            $patient->doctors()->wherePivot('clinic_id', $clinicUser->clinic_id)->detach();

            // If patient has no more assignments in any clinic, then delete the patient
            if ($patient->doctors()->count() === 0) {
                $patient->delete();
            }

            return $patient;
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function getPatientType($item): string
    {
        if ($item->isRegistered()) {
            return '<span class="badge bg-success">Registered User</span>';
        }
        return '<span class="badge bg-warning">Clinic Created</span>';
    }

    private function getAssignedDoctorsForClinic($item, $clinicId): string
    {
        // Filter doctors for this specific clinic
        $doctorsInClinic = $item->doctors->filter(function ($doctor) use ($clinicId) {
            return $doctor->pivot->clinic_id == $clinicId;
        });

        if ($doctorsInClinic->isEmpty()) {
            return '<span class="badge bg-secondary">No Doctor Assigned</span>';
        }

        $doctors = $doctorsInClinic->map(function ($doctor) {
            return '<span class="badge bg-primary me-1">' . $doctor->name . '</span>';
        })->implode('');

        return $doctors;
    }

    private function patientActions($item): string
    {
        $showUrl = route('clinic.patients.show', $item->id);
        $actions = '<div class="d-flex gap-2">';

        $actions .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';
        $actions .= '<button onclick="editPatient(' . $item->id . ')" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>';
        $actions .= '<button onclick="deletePatient(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';

        $actions .= '</div>';
        return $actions;
    }
}
