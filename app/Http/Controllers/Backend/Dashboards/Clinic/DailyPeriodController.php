<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\DailyPeriod\StoreDailyPeriodRequest;
use App\Http\Requests\Clinic\DailyPeriod\UpdateDailyPeriodRequest;
use App\Interfaces\Clinic\DailyPeriodRepositoryInterface;
use App\Models\DoctorProfile;
use App\Services\Appointment\PeriodGeneratorService;
use Illuminate\Http\Request;

class DailyPeriodController extends Controller
{
    protected $repo;
    protected $generatorService;

    public function __construct(
        DailyPeriodRepositoryInterface $repo,
        PeriodGeneratorService $generatorService
    ) {
        $this->repo = $repo;
        $this->generatorService = $generatorService;
    }

    public function index(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view daily periods'), 403, __('You are not authorized to view daily periods'));

        $clinicId = auth('clinic')->user()->clinic_id;
        $doctors = DoctorProfile::whereHas('clinicUser', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId);
        })->where('status', DoctorProfile::STATUS_APPROVED)->get();

        return view('backend.dashboards.clinic.pages.daily-periods.index', compact('doctors'));
    }

    public function data(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view daily periods'), 403, __('You are not authorized to view daily periods'));

        return $this->repo->data($request->all());
    }

    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create daily period'), 403, __('You are not authorized to create daily period'));

        $clinicId = auth('clinic')->user()->clinic_id;
        $doctors = DoctorProfile::whereHas('clinicUser', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId);
        })->where('status', DoctorProfile::STATUS_APPROVED)->get();

        return view('backend.dashboards.clinic.pages.daily-periods.create', compact('doctors'));
    }

    public function store(StoreDailyPeriodRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create daily period'), 403, __('You are not authorized to create daily period'));

        try {
            $period = $this->repo->store($request->validated());
            return redirect()->route('clinic.daily-periods.index')
                ->with('success', __('Daily period created successfully'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update daily period'), 403, __('You are not authorized to update daily period'));

        $period = $this->repo->find($id);

        $clinicId = auth('clinic')->user()->clinic_id;
        $doctors = DoctorProfile::whereHas('clinicUser', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId);
        })->where('status', DoctorProfile::STATUS_APPROVED)->get();

        return view('backend.dashboards.clinic.pages.daily-periods.edit', compact('period', 'doctors'));
    }

    public function update(UpdateDailyPeriodRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update daily period'), 403, __('You are not authorized to update daily period'));

        try {
            $period = $this->repo->update($id, $request->validated());
            return redirect()->route('clinic.daily-periods.index')
                ->with('success', __('Daily period updated successfully'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete daily period'), 403, __('You are not authorized to delete daily period'));

        try {
            $this->repo->destroy($id);
            return response()->json([
                'status' => 'success',
                'message' => __('Daily period deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleOpen($id)
    {
        try {
            $period = $this->repo->toggleOpen($id);
            return response()->json([
                'status' => 'success',
                'message' => __('Period status updated successfully'),
                'period' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateCapacity(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update daily period capacity'), 403, __('You are not authorized to update daily period capacity'));

        $request->validate(['capacity' => 'required|integer|min:1|max:100']);

        try {
            $period = $this->repo->updateCapacity($id, $request->capacity);
            return response()->json([
                'status' => 'success',
                'message' => __('Capacity updated successfully'),
                'period' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function generatePeriods(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('generate daily periods'), 403, __('You are not authorized to generate daily periods'));

        $request->validate([
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'days_ahead' => 'sometimes|integer|min:1|max:90'
        ]);

        try {
            $daysAhead = $request->get('days_ahead', 30);
            $count = $this->generatorService->generatePeriodsForDoctor(
                $request->doctor_profile_id,
                $daysAhead
            );

            return response()->json([
                'status' => 'success',
                'message' => __(':count periods generated successfully', ['count' => $count]),
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function viewAppointments($id)
    {
        // apply permissions
        abort_if(!hasPermission('view daily period appointments'), 403, __('You are not authorized to view daily period appointments'));

        try {
            $period = $this->repo->find($id);

            // Get all appointments for this period
            $appointments = $period->appointments()
                ->with(['patient.user', 'doctorProfile'])
                ->orderBy('slot_number')
                ->orderBy('created_at')
                ->get();

            // Calculate analytics
            $analytics = [
                'total_appointments' => $appointments->count(),
                'confirmed' => $appointments->where('status', 'confirmed')->count(),
                'pending' => $appointments->where('status', 'pending')->count(),
                'completed' => $appointments->where('status', 'completed')->count(),
                'cancelled' => $appointments->where('status', 'cancelled')->count(),
                'waiting' => $appointments->where('status', 'waiting')->count(),
                'capacity' => $period->capacity,
                'booked_count' => $period->booked_count,
                'available_slots' => $period->remaining_capacity,
                'capacity_percentage' => $period->capacity_percentage,
            ];

            // Visit type statistics
            $visitTypeStats = [
                'initial' => $appointments->where('visit_type', 0)->count(),
                'follow_up' => $appointments->where('visit_type', 1)->count(),
                'consultation' => $appointments->where('visit_type', 2)->count(),
            ];

            // Payment statistics
            $paymentStats = [
                'paid' => $appointments->where('payment_status', 'paid')->count(),
                'pending' => $appointments->where('payment_status', 'pending')->count(),
                'total_revenue' => $appointments->where('payment_status', 'paid')->sum('cost_amount'),
                'pending_revenue' => $appointments->where('payment_status', 'pending')->sum('cost_amount'),
            ];

            return view('backend.dashboards.clinic.pages.daily-periods.appointments', compact(
                'period',
                'appointments',
                'analytics',
                'visitTypeStats',
                'paymentStats'
            ));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}