<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Models\Prescription;

class PrescriptionController extends Controller
{
    public function index()
    {
        $patient = auth('patient')->user();
        $prescriptions = Prescription::with(['doctorProfile', 'items'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->paginate(10);

        return view('frontend.patient.prescriptions.index', compact('prescriptions', 'patient'));
    }

    public function show($id)
    {
        $patient = auth('patient')->user();
        $prescription = Prescription::with(['doctorProfile', 'appointment.period', 'items'])
            ->where('patient_id', $patient->id)
            ->findOrFail($id);

        return view('frontend.patient.prescriptions.show', compact('prescription', 'patient'));
    }
}

