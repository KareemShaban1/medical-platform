<?php

namespace App\Interfaces\User;

interface LabOrderRepositoryInterface
{
    public function listForPatient($patientId);
    public function showForPatient($patientId, $orderId);
}

