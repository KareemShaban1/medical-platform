<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Clinic\Appointment\UpdateAppointmentRequest;
use App\Interfaces\Clinic\AppointmentRepositoryInterface;
use App\Models\Appointment;
use App\Models\DailyPeriod;
use App\Models\DoctorProfile;
use App\Models\Patient;
use App\Services\Appointment\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $repo;
    protected $appointmentService;

    public function __construct(
        AppointmentRepositoryInterface $repo,
        AppointmentService $appointmentService
    ) {
        $this->repo = $repo;
        $this->appointmentService = $appointmentService;
    }

    public function index(Request $request)
    {
        $clinicId = auth('clinic')->user()->clinic_id;
        $doctors = DoctorProfile::whereHas('clinicUser', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId);
        })->where('status', DoctorProfile::STATUS_APPROVED)->get();

        $patients = Patient::all();

        return view('backend.dashboards.clinic.pages.appointments.index', compact('doctors', 'patients'));
    }

    public function data(Request $request)
    {
        return $this->repo->data($request->all());
    }

    public function store(StoreAppointmentRequest $request)
    {
        return $this->repo->store($request->validated());
    }

    public function show($id)
    {
        $appointment = $this->repo->find($id);

        return request()->ajax()
            ? response()->json($appointment->load(['doctorProfile', 'patient.user', 'period']))
            : view('backend.dashboards.clinic.pages.appointments.show', compact('appointment'));
    }

    public function update(UpdateAppointmentRequest $request, $id)
    {
        return $this->repo->update($id, $request->validated());
    }

    public function confirm($id)
    {
        try {
            $appointment = $this->repo->confirm($id);
            return response()->json([
                'status' => 'success',
                'message' => __('Appointment confirmed successfully'),
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        try {
            $appointment = $this->repo->cancel(
                $id,
                $request->reason,
                auth('clinic')->id()
            );

            return response()->json([
                'status' => 'success',
                'message' => __('Appointment cancelled successfully'),
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function forDoctor($doctorId, Request $request)
    {
        try {
            $filters = $request->only(['status', 'start_date', 'end_date']);
            $appointments = $this->repo->forDoctor($doctorId, $filters);
            return response()->json($appointments);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function forPeriod($periodId)
    {
        try {
            $appointments = $this->repo->forPeriod($periodId);
            return response()->json($appointments);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function getAvailablePeriods(Request $request)
    {
        $request->validate([
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'date' => 'required|date'
        ]);

        $periods = DailyPeriod::where('doctor_profile_id', $request->doctor_profile_id)
            ->where('date', $request->date)
            ->where('is_open', true)
            ->whereColumn('booked_count', '<', 'capacity')
            ->orderBy('start_time')
            ->get();

        return response()->json($periods);
    }
}

