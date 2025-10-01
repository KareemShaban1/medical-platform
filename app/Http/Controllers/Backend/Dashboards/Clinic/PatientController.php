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
        return view('backend.dashboards.clinic.pages.patients.index');
    }

    public function data()
    {
        return $this->patientRepo->data();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:patients,phone,NULL,id,clinic_id,' . auth('clinic')->user()->clinic_id,
            'email' => 'nullable|email|max:255|unique:patients,email',
            'password' => 'nullable|string|min:8',
        ]);

        try {
            $this->patientRepo->store($request->all());
            return $this->jsonResponse('success', __('Patient created successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $patient = $this->patientRepo->show($id);
        return view('backend.dashboards.clinic.pages.patients.show', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:patients,phone,' . $id . ',id,clinic_id,' . auth('clinic')->user()->clinic_id,
            'email' => 'nullable|email|max:255|unique:patients,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

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
