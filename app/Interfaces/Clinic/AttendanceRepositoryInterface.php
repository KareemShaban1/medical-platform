<?php

namespace App\Interfaces\Clinic;

interface AttendanceRepositoryInterface
{
    public function checkIn($userId, $at, $source, $notes = null);
    public function checkOut($userId, $at, $source, $notes = null);
    public function requestAbsence($userId, $at, $notes = null);
    public function approve($logId, $approverId);
    public function approveCheckIn($logId, $approverId);
    public function approveCheckOut($logId, $approverId);
    public function listForDay($date);
    public function pendingAbsences();
    public function listForUser($clinicUserId, $start, $end);
}
