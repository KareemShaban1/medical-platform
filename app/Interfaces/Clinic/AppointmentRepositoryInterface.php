<?php

namespace App\Interfaces\Clinic;

interface AppointmentRepositoryInterface
{
    public function index($filters = []);

    public function data($filters = []);

    public function store(array $data);

    public function update($id, array $data);

    public function destroy($id);

    public function find($id);

    public function forDoctor($doctorProfileId, $filters = []);

    public function forPatient($patientId, $filters = []);

    public function forPeriod($periodId);

    public function confirm($id);

    public function cancel($id, $reason = null, $cancelledBy = null);

    public function findByConfirmationCode($code);

    public function trashData($filters = []);

    public function restore($id);

    public function forceDelete($id);

    public function delete($id);
}

