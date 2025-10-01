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
        $patients = Patient::with(['clinic', 'user'])
            ->forClinic(auth('clinic')->user()->clinic_id);

        return datatables()->of($patients)
            ->addColumn('name', fn($item) => $item->user ? $item->user->name : 'N/A')
            ->addColumn('phone', fn($item) => $item->phone)
            ->addColumn('email', fn($item) => $item->user ? $item->user->email : 'N/A')
            ->addColumn('status', fn($item) => $item->status_badge)
            ->addColumn('type', fn($item) => $this->getPatientType($item))
            ->addColumn('action', fn($item) => $this->patientActions($item))
            ->rawColumns(['status', 'type', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;

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

            // Check if phone is already used in this clinic
            $existingPatient = Patient::where('clinic_id', auth('clinic')->user()->clinic_id)
                ->where('phone', $data['phone'])
                ->first();

            if ($existingPatient) {
                throw new \Exception('Phone number is already registered in this clinic.');
            }

            // Create patient record linked to user with phone stored in patients table
            $patient = Patient::create([
                'clinic_id' => auth('clinic')->user()->clinic_id,
                'user_id' => $user->id,
                'phone' => $data['phone'],
            ]);

            return $patient;
        });
    }

    public function show($id)
    {
        return Patient::with(['clinic', 'user'])
            ->forClinic(auth('clinic')->user()->clinic_id)
            ->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $patient = Patient::with('user')->forClinic(auth('clinic')->user()->clinic_id)->findOrFail($id);
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
                // Check if phone is already used in this clinic by another patient
                $existingPatient = Patient::where('clinic_id', auth('clinic')->user()->clinic_id)
                    ->where('phone', $data['phone'])
                    ->where('id', '!=', $patient->id)
                    ->first();

                if ($existingPatient) {
                    throw new \Exception('Phone number is already registered in this clinic.');
                }

                $patientUpdateData['phone'] = $data['phone'];
            }

            if (!empty($patientUpdateData)) {
                $patient->update($patientUpdateData);
            }

            return $patient->refresh();
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $patient = Patient::forClinic(auth('clinic')->user()->clinic_id)->findOrFail($id);
            $patient->delete();
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
