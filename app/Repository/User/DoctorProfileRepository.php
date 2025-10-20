<?php

namespace App\Repository\User;

use App\Interfaces\User\DoctorProfileRepositoryInterface;
use App\Models\DoctorProfile;
use App\Models\DailyPeriod;
use Illuminate\Support\Facades\DB;

class DoctorProfileRepository implements DoctorProfileRepositoryInterface
{
    public function index($filters = [])
    {
        $query = DoctorProfile::query()
            ->where('status', DoctorProfile::STATUS_APPROVED)
            ->with(['clinicUser.clinic', 'speciality']);

        if (!empty($filters['speciality_id'])) {
            $query->where('speciality_id', $filters['speciality_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['clinic_id'])) {
            $query->whereHas('clinicUser', function ($q) use ($filters) {
                $q->where('clinic_id', $filters['clinic_id']);
            });
        }

        return $query->orderBy('is_featured', 'desc')
            ->orderBy('name')
            ->paginate(12);
    }

    public function find($id)
    {
        return DoctorProfile::where('status', DoctorProfile::STATUS_APPROVED)
            ->with(['clinicUser.clinic', 'speciality'])
            ->findOrFail($id);
    }

    public function forClinic($clinicId)
    {
        return DoctorProfile::where('status', DoctorProfile::STATUS_APPROVED)
            ->whereHas('clinicUser', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })
            ->with(['speciality'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('name')
            ->get();
    }

    public function getAvailableDays($doctorProfileId, $startDate, $endDate)
    {
        // Get all open days, regardless of capacity
        // Filter out past dates
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        $periods = DailyPeriod::where('doctor_profile_id', $doctorProfileId)
            ->where('is_open', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Group by date and filter out dates where all periods are in the past
        $availableDays = $periods->groupBy(function($period) {
            return $period->date->format('Y-m-d');
        })->filter(function($dayPeriods, $date) use ($today, $currentTime) {
            // If date is in the future, include it
            if ($date > $today) {
                return true;
            }

            // If date is today, check if any period hasn't started yet
            if ($date === $today) {
                return $dayPeriods->filter(function($period) use ($currentTime) {
                    return $period->start_time > $currentTime;
                })->isNotEmpty();
            }

            // Past dates are excluded
            return false;
        })->keys()->toArray();

        return $availableDays;
    }

    public function getAvailablePeriods($doctorProfileId, $date)
    {
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        $periods = DailyPeriod::where('doctor_profile_id', $doctorProfileId)
            ->where('date', $date)
            ->where('is_open', true)
            ->orderBy('start_time')
            ->get();

        // Filter out past periods for today
        if ($date === $today) {
            $periods = $periods->filter(function($period) use ($currentTime) {
                return $period->start_time > $currentTime;
            })->values();
        }

        // Add is_past flag to each period for frontend
        return $periods->map(function($period) use ($date, $today, $currentTime) {
            $period->is_past = ($date < $today) || ($date === $today && $period->start_time <= $currentTime);

            // Calculate confirmed bookings only
            $confirmedCount = \App\Models\Appointment::where('period_id', $period->id)
                ->where('status', \App\Models\Appointment::STATUS_CONFIRMED)
                ->count();

            $period->confirmed_bookings = $confirmedCount;
            $period->remaining_capacity = max(0, $period->capacity - $confirmedCount);

            return $period;
        });
    }
}

