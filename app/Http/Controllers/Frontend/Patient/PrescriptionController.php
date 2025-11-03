<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $patient = auth('patient')->user();
        $prescriptions = Prescription::with(['doctorProfile', 'items'])
            ->where('patient_id', $patient->id)
            ->when($request->filled('from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('from'));
            })
            ->when($request->filled('to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('to'));
            })
            ->latest()
            ->paginate(10)
            ->appends($request->only(['from', 'to']));

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
