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
        $visitTypes = Appointment::getVisitTypeOptions();

        return view('backend.dashboards.clinic.pages.appointments.index', compact('doctors', 'patients', 'visitTypes'));
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

    public function analytics(Request $request, $doctorId = null)
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        // Get all doctors in the clinic
        $doctors = DoctorProfile::whereHas('clinicUser', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId);
        })->where('status', DoctorProfile::STATUS_APPROVED)->get();

        // Set default doctor if not provided
        if (!$doctorId && $doctors->isNotEmpty()) {
            $doctorId = $doctors->first()->id;
        }

        $selectedDoctor = DoctorProfile::find($doctorId);

        // Date range filters
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        if (!$selectedDoctor) {
            return view('backend.dashboards.clinic.pages.appointments.analytics', [
                'doctors' => $doctors,
                'selectedDoctor' => null,
                'analytics' => [],
                'appointments' => collect([]),
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);
        }

        // Get appointments for the doctor within date range
        $appointments = Appointment::where('doctor_profile_id', $doctorId)
            ->whereHas('period', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })
            ->with(['patient.user', 'period'])
            ->get();

        // Calculate comprehensive analytics
        $analytics = [
            // Overview
            'total_appointments' => $appointments->count(),
            'confirmed' => $appointments->where('status', 'confirmed')->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'waiting' => $appointments->where('status', 'waiting')->count(),

            // Visit Types
            'initial_visits' => $appointments->where('visit_type', 0)->count(),
            'follow_ups' => $appointments->where('visit_type', 1)->count(),
            'consultations' => $appointments->where('visit_type', 2)->count(),

            // Revenue
            'total_revenue' => $appointments->where('payment_status', 'paid')->sum('cost_amount'),
            'pending_revenue' => $appointments->where('payment_status', 'pending')->sum('cost_amount'),
            'average_cost' => $appointments->where('cost_amount', '>', 0)->avg('cost_amount'),
            'paid_count' => $appointments->where('payment_status', 'paid')->count(),
            'pending_payment_count' => $appointments->where('payment_status', 'pending')->count(),

            // Time-based
            'appointments_by_date' => $appointments->groupBy(function($item) {
                return $item->period->date->format('Y-m-d');
            })->map->count(),

            'appointments_by_status_and_date' => $appointments->groupBy(function($item) {
                return $item->period->date->format('Y-m-d');
            })->map(function($dateAppointments) {
                return [
                    'confirmed' => $dateAppointments->where('status', 'confirmed')->count(),
                    'completed' => $dateAppointments->where('status', 'completed')->count(),
                    'cancelled' => $dateAppointments->where('status', 'cancelled')->count(),
                ];
            }),

            // Peak times
            'busiest_day' => $appointments->groupBy(function($item) {
                return $item->period->date->format('l');
            })->map->count()->sortDesc()->keys()->first(),

            // Completion rate
            'completion_rate' => $appointments->count() > 0
                ? round(($appointments->where('status', 'completed')->count() / $appointments->count()) * 100, 2)
                : 0,

            'cancellation_rate' => $appointments->count() > 0
                ? round(($appointments->where('status', 'cancelled')->count() / $appointments->count()) * 100, 2)
                : 0,

            // Recent appointments
            'recent_appointments' => $appointments->sortByDesc('created_at')->take(5),
        ];

        return view('backend.dashboards.clinic.pages.appointments.analytics', compact(
            'doctors',
            'selectedDoctor',
            'analytics',
            'appointments',
            'startDate',
            'endDate'
        ));
    }
}

