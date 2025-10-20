<?php

namespace App\Interfaces\Clinic;

interface DailyPeriodRepositoryInterface
{
    public function index($filters = []);

    public function data($filters = []);

    public function store(array $data);

    public function update($id, array $data);

    public function destroy($id);

    public function find($id);

    public function forDoctor($doctorProfileId, $filters = []);

    public function forDateRange($doctorProfileId, $startDate, $endDate);

    public function toggleOpen($id);

    public function updateCapacity($id, $capacity);
}

