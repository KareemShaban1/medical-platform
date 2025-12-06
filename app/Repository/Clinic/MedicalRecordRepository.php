<?php

namespace App\Repository\Clinic;

use App\Http\Requests\Clinic\MedicalRecord\UpdateMedicalRecordRequest;
use App\Interfaces\Clinic\MedicalRecordRepositoryInterface;
use App\Models\Appointment;
use App\Models\MedicalRecord;

class MedicalRecordRepository implements MedicalRecordRepositoryInterface
{
    public function data()
    {
        $clinicId = auth('clinic')->user()->clinic_id;
        $query = MedicalRecord::with(['appointment.period:id,date', 'doctor:id,name', 'patient'])
            ->where('clinic_id', $clinicId)
            ->latest('updated_at');

        return datatables()->of($query)
            ->addColumn('appointment_date', fn($r) => optional(optional($r->appointment)->period->date)->format('Y-m-d') ?: 'N/A')
            ->addColumn('patient_name', fn($r) => $r->patient->name ?? 'N/A')
            ->addColumn('doctor_name', fn($r) => $r->doctor->name ?? 'N/A')
            ->addColumn('visit_type_label', fn($r) => [0=>'Initial',1=>'Follow-up',2=>'Consultation'][$r->visit_type] ?? 'N/A')
            ->addColumn('shared_badge', function ($r) {
                $label = $r->is_shared_with_patient ? __('Yes') : __('No');
                $cls = $r->is_shared_with_patient ? 'success' : 'secondary';
                return '<span class="badge bg-' . $cls . '">' . e($label) . '</span>';
            })
            ->addColumn('actions', function ($r) {
                $html = '<div class="d-flex gap-2">';

                if (hasPermission('update medical record')) {
                    $editUrl = route('clinic.medical-records.edit', $r->appointment_id);
                    $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary">' . __('Edit') . '</a>';
                }

                if (hasPermission('share medical record')) {
                    $shareUrl = route('clinic.medical-records.share', $r->id);
                    $shareLabel = $r->is_shared_with_patient ? __('Unshare') : __('Share');
                    $html .= '<form method="POST" action="' . $shareUrl . '" class="d-inline">';
                    $html .= csrf_field();
                    $html .= '<button class="btn btn-sm btn-outline-secondary">' . e($shareLabel) . '</button>';
                    $html .= '</form>';
                }

                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['shared_badge', 'actions'])
            ->make(true);
    }
    public function index()
    {
        $clinicUser = auth('clinic')->user();
        $records = MedicalRecord::with(['appointment.period', 'doctor', 'patient'])
            ->where('clinic_id', $clinicUser->clinic_id)
            ->latest('updated_at')
            ->paginate(20);

        return view('backend.dashboards.clinic.pages.medical-records.index', compact('records'));
    }

    public function edit(Appointment $appointment)
    {
        $clinicUser = auth('clinic')->user();

        abort_unless(optional($appointment->doctorProfile->clinic)->id === $clinicUser->clinic_id, 403);

        $record = MedicalRecord::firstOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'clinic_id' => $clinicUser->clinic_id,
                'doctor_profile_id' => $appointment->doctor_profile_id,
                'patient_id' => $appointment->patient_id,
                'visit_type' =>($appointment->visit_type ?? 0),
                'created_by' => $clinicUser->id,
            ]
        );

        return view('backend.dashboards.clinic.pages.medical-records.edit', compact('appointment', 'record'));
    }

    public function update(UpdateMedicalRecordRequest $request, Appointment $appointment)
    {
        $clinicUser = auth('clinic')->user();

        abort_unless(optional($appointment->doctorProfile->clinic)->id === $clinicUser->clinic_id, 403);

        $record = MedicalRecord::where('appointment_id', $appointment->id)->firstOrFail();

        $data = $request->validated();
        $record->fill([
            'visit_type' =>  $data['visit_type'],
            'chief_complaint' => $data['chief_complaint'] ?? null,
            'diagnosis' => $data['diagnosis'] ?? null,
            'treatment' => $data['treatment'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_shared_with_patient' => (bool) ($data['is_shared_with_patient'] ?? $record->is_shared_with_patient),
            'updated_by' => $clinicUser->id,
        ])->save();

        return back()->with('success', __('Medical record updated successfully'));
    }

    public function toggleShare(MedicalRecord $record)
    {
        $clinicUser = auth('clinic')->user();

        abort_unless($record->clinic_id === $clinicUser->clinic_id, 403);

        $record->is_shared_with_patient = !$record->is_shared_with_patient;
        $record->updated_by = $clinicUser->id;
        $record->save();

        return back()->with('success', $record->is_shared_with_patient
            ? __('Record shared with patient')
            : __('Record unshared from patient'));
    }
}