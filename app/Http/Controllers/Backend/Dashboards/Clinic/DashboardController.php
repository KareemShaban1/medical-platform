<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\DoctorProfile;
use App\Models\ClinicUser;
use App\Models\Expense;
use App\Models\Request as PurchaseRequest;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $clinic = auth('clinic')->user()->clinic;
        $clinicId = $clinic->id;
        $user = auth('clinic')->user();

        // Announcement
        $announcement = Announcement::active()
            ->where(function ($q) use ($clinic) {
                $q->where('target_clinics_all', true)
                    ->orWhereHas('clinics', function ($q) use ($clinic) {
                        $q->where('clinics.id', $clinic->id);
                    });
            })
            ->whereDoesntHave('dismissals', function ($q) use ($clinic) {
                $q->where('dismissable_type', \App\Models\Clinic::class)
                    ->where('dismissable_id', $clinic->id);
            })
            ->latest('created_at')
            ->first();

        // Stats Cards Data
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Get doctor profile IDs for this clinic
        $doctorProfileIds = DoctorProfile::whereHas('clinicUser', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId);
        })->pluck('id')->toArray();

        // Appointments Stats - filter by doctor_profile_id (which belongs to the clinic)
        $totalAppointmentsToday = 0;
        $confirmedAppointmentsToday = 0;
        $pendingAppointmentsToday = 0;
        $totalAppointmentsMonth = 0;

        if (!empty($doctorProfileIds)) {
            $totalAppointmentsToday = Appointment::whereIn('doctor_profile_id', $doctorProfileIds)
                ->whereHas('period', function ($q) use ($today) {
                    $q->whereDate('date', $today);
                })
                ->count();

            $confirmedAppointmentsToday = Appointment::whereIn('doctor_profile_id', $doctorProfileIds)
                ->whereHas('period', function ($q) use ($today) {
                    $q->whereDate('date', $today);
                })
                ->where('status', 'confirmed')
                ->count();

            $pendingAppointmentsToday = Appointment::whereIn('doctor_profile_id', $doctorProfileIds)
                ->whereHas('period', function ($q) use ($today) {
                    $q->whereDate('date', $today);
                })
                ->where('status', 'pending')
                ->count();

            $totalAppointmentsMonth = Appointment::whereIn('doctor_profile_id', $doctorProfileIds)
                ->whereHas('period', function ($q) use ($thisMonth) {
                    $q->where('date', '>=', $thisMonth);
                })
                ->count();
        }

        // Patients Stats - Patients are linked to clinics through doctor_patient pivot table
        // Use the scopeForClinic on Patient model
        $totalPatients = Patient::forClinic($clinicId)->count();
        $newPatientsThisMonth = Patient::forClinic($clinicId)
            ->where('patients.created_at', '>=', $thisMonth)
            ->count();
        $newPatientsLastMonth = Patient::forClinic($clinicId)
            ->whereBetween('patients.created_at', [$lastMonth, $endOfLastMonth])
            ->count();
        $patientsGrowth = $newPatientsLastMonth > 0
            ? round((($newPatientsThisMonth - $newPatientsLastMonth) / $newPatientsLastMonth) * 100, 1)
            : ($newPatientsThisMonth > 0 ? 100 : 0);

        // Doctors Stats
        $totalDoctors = count($doctorProfileIds);

        $activeDoctors = DoctorProfile::whereHas('clinicUser', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId)->where('status', true);
        })->where('status', 'approved')->count();

        // Users/Staff Stats
        $totalStaff = ClinicUser::where('clinic_id', $clinicId)->count();
        $activeStaff = ClinicUser::where('clinic_id', $clinicId)->where('status', true)->count();

        // Expenses Stats
        $expensesThisMonth = Expense::where('clinic_id', $clinicId)
            ->where('expense_date', '>=', $thisMonth)
            ->sum('amount') ?? 0;

        $expensesLastMonth = Expense::where('clinic_id', $clinicId)
            ->whereBetween('expense_date', [$lastMonth, $endOfLastMonth])
            ->sum('amount') ?? 0;

        // Purchase Requests Stats
        $activePurchaseRequests = PurchaseRequest::where('clinic_id', $clinicId)
            ->where('status', 'open')
            ->count();

        $pendingOffers = PurchaseRequest::where('clinic_id', $clinicId)
            ->where('status', 'open')
            ->whereHas('offers', function ($q) {
                $q->where('status', 'pending');
            })
            ->count();

        // Subscription Info
        $subscription = Subscription::where('subscribable_type', \App\Models\Clinic::class)
            ->where('subscribable_id', $clinicId)
            ->where('status', 'active')
            ->with('plan')
            ->first();

        // Weekly Appointments Chart Data
        $weeklyAppointments = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = 0;
            if (!empty($doctorProfileIds)) {
                $count = Appointment::whereIn('doctor_profile_id', $doctorProfileIds)
                    ->whereHas('period', function ($q) use ($date) {
                        $q->whereDate('date', $date);
                    })
                    ->count();
            }
            $weeklyAppointments[] = [
                'date' => $date->format('D'),
                'count' => $count
            ];
        }

        // Appointment Status Distribution
        $appointmentStatusData = [];
        if (!empty($doctorProfileIds)) {
            $appointmentStatusData = Appointment::whereIn('doctor_profile_id', $doctorProfileIds)
                ->whereHas('period', function ($q) use ($thisMonth) {
                    $q->where('date', '>=', $thisMonth);
                })
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        }

        // Recent Appointments
        $recentAppointments = collect();
        if (!empty($doctorProfileIds)) {
            $recentAppointments = Appointment::whereIn('doctor_profile_id', $doctorProfileIds)
                ->with(['patient.user', 'doctorProfile', 'period'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        // Recent Patients
        $recentPatients = Patient::forClinic($clinicId)
            ->with('user')
            ->orderBy('patients.created_at', 'desc')
            ->take(5)
            ->get();

        // Upcoming Appointments (next 7 days)
        $upcomingAppointments = collect();
        if (!empty($doctorProfileIds)) {
            $upcomingAppointments = Appointment::whereIn('doctor_profile_id', $doctorProfileIds)
                ->whereHas('period', function ($q) use ($today) {
                    $q->where('date', '>=', $today)
                        ->where('date', '<=', Carbon::today()->addDays(7));
                })
                ->whereIn('status', ['pending', 'confirmed'])
                ->with(['patient.user', 'doctorProfile', 'period'])
                ->take(5)
                ->get()
                ->sortBy(function ($apt) {
                    return optional($apt->period)->date;
                });
        }

        // Quick Actions based on permissions
        $quickActions = $this->getClinicQuickActions($user);

        return view('backend.dashboards.clinic.pages.dashboard', compact(
            'announcement',
            'clinic',
            'totalAppointmentsToday',
            'confirmedAppointmentsToday',
            'pendingAppointmentsToday',
            'totalAppointmentsMonth',
            'totalPatients',
            'newPatientsThisMonth',
            'patientsGrowth',
            'totalDoctors',
            'activeDoctors',
            'totalStaff',
            'activeStaff',
            'expensesThisMonth',
            'expensesLastMonth',
            'activePurchaseRequests',
            'pendingOffers',
            'subscription',
            'weeklyAppointments',
            'appointmentStatusData',
            'recentAppointments',
            'recentPatients',
            'upcomingAppointments',
            'quickActions'
        ));
    }

    private function getClinicQuickActions($user)
    {
        $actions = [];

        // Always available actions
        $actions[] = [
            'title' => __('Add Patient'),
            'icon' => 'fas fa-user-plus',
            'route' => 'clinic.patients.index',
            'color' => 'primary',
            'description' => __('Register a new patient')
        ];

        $actions[] = [
            'title' => __('View Appointments'),
            'icon' => 'fas fa-calendar-alt',
            'route' => 'clinic.appointments.index',
            'color' => 'success',
            'description' => __('Manage appointments')
        ];

        $actions[] = [
            'title' => __('Doctor Profiles'),
            'icon' => 'fas fa-user-md',
            'route' => 'clinic.doctor-profiles.index',
            'color' => 'info',
            'description' => __('Manage doctor profiles')
        ];

        $actions[] = [
            'title' => __('View Expenses'),
            'icon' => 'fas fa-receipt',
            'route' => 'clinic.expenses.index',
            'color' => 'warning',
            'description' => __('Track clinic expenses')
        ];

        $actions[] = [
            'title' => __('Purchase Requests'),
            'icon' => 'fas fa-shopping-cart',
            'route' => 'clinic.requests.index',
            'color' => 'danger',
            'description' => __('Create purchase requests')
        ];

        $actions[] = [
            'title' => __('Clinic Users'),
            'icon' => 'fas fa-users',
            'route' => 'clinic.users.index',
            'color' => 'secondary',
            'description' => __('Manage staff members')
        ];

        return $actions;
    }
}
