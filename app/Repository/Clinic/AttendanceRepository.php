<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\AttendanceRepositoryInterface;
use App\Models\AttendanceLog;
use App\Models\ClinicUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    private function assertBelongsToClinic($clinicUserId): void
    {
        $exists = ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)
            ->where('id', $clinicUserId)->exists();
        if (!$exists) throw new \Exception(__('Unauthorized'));
    }

    public function checkIn($userId, $at, $source, $notes = null)
    {
        $this->assertBelongsToClinic($userId);
        return AttendanceLog::create([
            'clinic_user_id' => $userId,
            'check_type' => 'check_in',
            'source' => $source,
            'at' => $at,
            'notes' => $notes,
            'requested_by' => auth('clinic')->id(),
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function checkOut($userId, $at, $source, $notes = null)
    {
        $this->assertBelongsToClinic($userId);
        $date = Carbon::parse($at)->toDateString();
        // Must have a check-in today and checkout cannot be before it
        $lastCheckIn = AttendanceLog::where('clinic_user_id', $userId)
            ->where('check_type', 'check_in')
            ->whereDate('at', $date)
            ->orderByDesc('at')->first();

        if (!$lastCheckIn) {
            throw new \Exception(__('No check-in found for today'));
        }
        if ($lastCheckIn->at->gt($at)) {
            throw new \Exception(__('Checkout cannot predate today\'s check-in'));
        }
        if (is_null($lastCheckIn->approved_at)) {
            throw new \Exception(__('Check-in is pending approval'));
        }

        return AttendanceLog::create([
            'clinic_user_id' => $userId,
            'check_type' => 'check_out',
            'source' => $source,
            'at' => $at,
            'notes' => $notes,
            'requested_by' => auth('clinic')->id(),
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function requestAbsence($userId, $at, $notes = null)
    {
        $this->assertBelongsToClinic($userId);
        return AttendanceLog::create([
            'clinic_user_id' => $userId,
            'check_type' => 'absence_request',
            'source' => 'web',
            'at' => $at,
            'notes' => $notes,
            'requested_by' => auth('clinic')->id(),
        ]);
    }

    public function approve($logId, $approverId)
    {
        $log = AttendanceLog::findOrFail($logId);
        $this->assertBelongsToClinic($log->clinic_user_id);
        if ($log->check_type !== 'absence_request') {
            throw new \Exception(__('Only absence requests can be approved'));
        }
        $log->update([
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
        return $log->refresh();
    }

    public function approveCheckIn($logId, $approverId)
    {
        $log = AttendanceLog::findOrFail($logId);
        $this->assertBelongsToClinic($log->clinic_user_id);
        if ($log->check_type !== 'check_in') {
            throw new \Exception(__('Only check-in logs can be approved'));
        }
        if (!is_null($log->approved_at)) {
            return $log;
        }
        $log->update([
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
        return $log->refresh();
    }

    public function approveCheckOut($logId, $approverId)
    {
        $log = AttendanceLog::findOrFail($logId);
        $this->assertBelongsToClinic($log->clinic_user_id);
        if ($log->check_type !== 'check_out') {
            throw new \Exception(__('Only check-out logs can be approved'));
        }
        if (!is_null($log->approved_at)) {
            return $log;
        }
        $log->update([
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
        return $log->refresh();
    }

    public function listForDay($date)
    {
        return AttendanceLog::with(['clinicUser'])
            ->withCount('media')
            ->whereHas('clinicUser', function ($q) {
                $q->where('clinic_id', auth('clinic')->user()->clinic_id);
            })
            ->whereDate('at', $date)
            ->orderBy('at')
            ->get();
    }

    public function pendingAbsences()
    {
        return AttendanceLog::with(['clinicUser', 'requester'])
            ->withCount('media')
            ->whereHas('clinicUser', function ($q) {
                $q->where('clinic_id', auth('clinic')->user()->clinic_id);
            })
            ->where('check_type', 'absence_request')
            ->whereNull('approved_at')
            ->orderBy('at', 'desc')
            ->get();
    }

    public function listForUser($clinicUserId, $start, $end)
    {
        $this->assertBelongsToClinic($clinicUserId);
        return AttendanceLog::with(['approver', 'requester'])
            ->where('clinic_user_id', $clinicUserId)
            ->whereIn('check_type', ['check_in','check_out'])
            ->whereBetween('at', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()])
            ->orderBy('at', 'asc')
            ->get();
    }
}
