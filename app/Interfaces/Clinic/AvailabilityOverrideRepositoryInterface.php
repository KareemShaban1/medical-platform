<?php

namespace App\Interfaces\Clinic;

interface AvailabilityOverrideRepositoryInterface
{
    public function index($filters = []);

    public function data($filters = []);

    public function store(array $data);

    public function update($id, array $data);

    public function destroy($id);

    public function find($id);

    public function trash();

    public function trashData();

    public function restore($id);

    public function forceDelete($id);

    public function forDoctor($doctorProfileId, $filters = []);

    public function forDateRange($doctorProfileId, $startDate, $endDate);
}

