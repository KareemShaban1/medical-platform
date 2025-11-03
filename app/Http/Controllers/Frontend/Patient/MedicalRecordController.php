<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $patient = auth('patient')->user();
        $records = MedicalRecord::with(['appointment.period', 'doctor'])
            ->where('patient_id', $patient->id)
            ->where('is_shared_with_patient', true)
            ->when($request->filled('from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('from'));
            })
            ->when($request->filled('to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('to'));
            })
            ->latest('created_at')
            ->get();

        return view('frontend.patient.medical-records.index', compact('records', 'patient'));
    }

    public function show(MedicalRecord $record)
    {
        $patient = auth('patient')->user();

        abort_unless($record->patient_id === $patient->id && $record->is_shared_with_patient, 404);

        return view('frontend.patient.medical-records.show', compact('record', 'patient'));
    }
}
