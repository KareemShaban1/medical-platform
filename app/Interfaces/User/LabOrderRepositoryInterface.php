<?php

namespace App\Interfaces\User;

interface LabOrderRepositoryInterface
{
    public function listForPatient($patientId, ?string $from = null, ?string $to = null);
    public function showForPatient($patientId, $orderId);
}
