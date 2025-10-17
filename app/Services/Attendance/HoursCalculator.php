<?php

namespace App\Services\Attendance;

use App\Models\AttendanceLog;

class HoursCalculator
{
    /**
     * Compute total payable seconds for a user within [start, end] date range.
     * Returns ['totalSeconds' => int, 'anomalies' => array]
     */
    public function compute(int $clinicUserId, string $startDate, string $endDate): array
    {
        $logs = AttendanceLog::where('clinic_user_id', $clinicUserId)
            ->whereBetween('at', [$startDate, $endDate])
            ->orderBy('at')
            ->get();

        $total = 0;
        $openCheckIn = null;
        $anomalies = [];

        foreach ($logs as $log) {
            if ($log->check_type === 'check_in') {
                if ($openCheckIn) {
                    $anomalies[] = ['type' => 'double_check_in', 'at' => $log->at];
                }
                $openCheckIn = $log->at;
            } elseif ($log->check_type === 'check_out') {
                if (!$openCheckIn) {
                    $anomalies[] = ['type' => 'checkout_without_checkin', 'at' => $log->at];
                    continue;
                }
                // Compute signed diff to avoid unexpected negatives from TZ or serialization quirks
                $seconds = $log->at->getTimestamp() - $openCheckIn->getTimestamp();
                if ($seconds < 0) {
                    $anomalies[] = ['type' => 'checkout_before_checkin', 'at' => $log->at];
                    $openCheckIn = null;
                    continue;
                }
                $total += $seconds;
                $openCheckIn = null;
            }
        }

        if ($openCheckIn) {
            $anomalies[] = ['type' => 'missing_checkout', 'at' => $openCheckIn];
        }

        return [
            'totalSeconds' => $total,
            'anomalies' => $anomalies,
        ];
    }
}
