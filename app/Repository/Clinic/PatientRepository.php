<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\PatientRepositoryInterface;
use App\Models\Patient;
use App\Models\DoctorPatient;
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
            $patients = Patient::query()
                ->forDoctorInClinic($doctorProfile->id, $clinicUser->clinic_id)
                ->leftJoin('users', 'patients.user_id', '=', 'users.id')
                ->select('patients.*')
                ->with(['user', 'doctors'])
                ->distinct();
        } else {
            // Show all patients in the clinic
            $patients = Patient::query()
                ->forClinic($clinicUser->clinic_id)
                ->leftJoin('users', 'patients.user_id', '=', 'users.id')
                ->select('patients.*')
                ->with(['user', 'doctors'])
                ->distinct();
        }

        return datatables()->of($patients)
            ->filterColumn('name', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('users.name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('email', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('users.email', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('phone', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('patients.phone', 'like', "%{$keyword}%");
                });
            })
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

    public function edit($request, $id)
    {
        $clinicUser = auth('clinic')->user();

        $patient = Patient::with(['user', 'doctors'])
            ->forClinic($clinicUser->clinic_id)
            ->findOrFail($id);

        $assignedDoctorIds = $patient->doctors->pluck('id')->toArray();

        if ($request->ajax()) {
            return response()->json([
                'id'     => $patient->id,
                'name'   => $patient->user->name ?? null,
                'phone'  => $patient->user->phone ?? null,
                'email'  => $patient->user->email ?? null,
                'doctors'=> $patient->doctors->map(function ($doctor) {
                    return [
                        'id'   => $doctor->id,
                        'name' => $doctor->name,
                    ];
                }),
                'assigned_doctor_ids' => $assignedDoctorIds,
            ]);
        }

        return $patient;
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

            // Soft delete doctor-patient assignments for this clinic only (move to trash)
            $assignments = DoctorPatient::where('patient_id', $patient->id)
                ->where('clinic_id', $clinicUser->clinic_id)
                ->when($clinicUser->isDoctor(), function($q) use ($clinicUser) {
                    $q->where('doctor_profile_id', $clinicUser->getDoctorProfile()->id);
                })
                ->whereNull('deleted_at')
                ->get();

            foreach ($assignments as $assignment) {
                $assignment->delete();
            }

            // If patient has no more ACTIVE assignments in any clinic, soft-delete the patient
            $activeAssignmentsExist = DoctorPatient::where('patient_id', $patient->id)
                ->whereNull('deleted_at')
                ->exists();
            if (!$activeAssignmentsExist) {
                $patient->delete();
            }

            return $patient;
        });
    }

    public function trashData()
    {
        $clinicUser = auth('clinic')->user();

        // Base query for trashed assignments in this clinic
        $trashedAssignmentsQuery = DoctorPatient::onlyTrashed()
            ->where('clinic_id', $clinicUser->clinic_id)
            ->with(['patient' => function($query) {
                $query->withTrashed()->with('user'); // Include soft-deleted patients
            }, 'doctorProfile']);

        // Check if the logged-in user is a doctor
        if ($clinicUser->isDoctor()) {
            // Show only trashed patients that were assigned to this doctor
            $doctorProfile = $clinicUser->getDoctorProfile();
            $trashedAssignmentsQuery->where('doctor_profile_id', $doctorProfile->id);
        }

        $trashedAssignments = $trashedAssignmentsQuery->get();

        // Group by patient to present a single row per patient with aggregated doctors
        $grouped = $trashedAssignments->groupBy('patient_id')->map(function ($items, $patientId) {
            $first = $items->first();
            return (object) [
                'id' => $first->patient ? $first->patient->id : $patientId,
                'patient' => $first->patient,
                'doctors' => $items->pluck('doctorProfile')->filter(),
            ];
        })->values();

        return datatables()->of($grouped)
            ->addColumn('name', function ($row) {
                return $row->patient && $row->patient->user ? $row->patient->user->name : 'N/A';
            })
            ->addColumn('phone', function ($row) {
                return $row->patient ? $row->patient->phone : 'N/A';
            })
            ->addColumn('email', function ($row) {
                return $row->patient && $row->patient->user ? $row->patient->user->email : 'N/A';
            })
            ->addColumn('assigned_doctors', function ($row) {
                if (!$row->doctors || $row->doctors->isEmpty()) {
                    return '<span class="badge bg-secondary">No Doctor</span>';
                }
                return $row->doctors->map(function ($doctor) {
                    return '<span class="badge bg-primary me-1">' . e($doctor->name) . '</span>';
                })->implode('');
            })
            ->addColumn('trash_action', function ($row) {
                $actions = '<div class="d-flex gap-2">';
                $actions .= '<button onclick="restore(' . $row->id . ')" class="btn btn-sm btn-success" title="Restore"><i class="mdi mdi-restore"></i></button>';
                $actions .= '<button onclick="forceDelete(' . $row->id . ')" class="btn btn-sm btn-danger" title="Delete Permanently"><i class="mdi mdi-delete-forever"></i></button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['assigned_doctors', 'trash_action'])
            ->make(true);
    }

    public function restore($patientId)
    {
        return DB::transaction(function () use ($patientId) {
            $clinicUser = auth('clinic')->user();

            // Restore patient (if soft-deleted) when restoring assignments
            $patient = Patient::withTrashed()->findOrFail($patientId);
            if ($patient->trashed()) {
                $patient->restore();
            }

            $restoreAssignments = DoctorPatient::onlyTrashed()
                ->where('patient_id', $patientId)
                ->where('clinic_id', $clinicUser->clinic_id)
                ->when($clinicUser->isDoctor(), function($q) use ($clinicUser) {
                    $q->where('doctor_profile_id', $clinicUser->getDoctorProfile()->id);
                })
                ->get();

            foreach ($restoreAssignments as $assignment) {
                $assignment->restore();
            }

            return $patient;
        });
    }

    public function forceDelete($patientId)
    {
        return DB::transaction(function () use ($patientId) {
            $clinicUser = auth('clinic')->user();

            // Permanently remove trashed assignments for this clinic (scope by doctor if applicable)
            $forceAssignments = DoctorPatient::onlyTrashed()
                ->where('patient_id', $patientId)
                ->where('clinic_id', $clinicUser->clinic_id)
                ->when($clinicUser->isDoctor(), function($q) use ($clinicUser) {
                    $q->where('doctor_profile_id', $clinicUser->getDoctorProfile()->id);
                })
                ->get();

            foreach ($forceAssignments as $assignment) {
                $assignment->forceDelete();
            }

            // If patient now has no assignments at all (active or trashed) in any clinic, we may leave patient record as-is.
            return true;
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
