<?php

namespace App\Interfaces\User;

interface DoctorProfileRepositoryInterface
{
    public function index($filters = []);

    public function find($id);

    public function forClinic($clinicId);

    public function getAvailableDays($doctorProfileId, $startDate, $endDate);

    public function getAvailablePeriods($doctorProfileId, $date);
}

