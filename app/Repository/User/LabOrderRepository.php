<?php

namespace App\Repository\User;

use App\Interfaces\User\LabOrderRepositoryInterface;
use App\Models\LabOrder;

class LabOrderRepository implements LabOrderRepositoryInterface
{
    public function listForPatient($patientId)
    {
        return LabOrder::with(['clinic'])
            ->forPatient($patientId)
            ->where('status', 'completed')
            ->latest()
            ->get();
    }

    public function showForPatient($patientId, $orderId)
    {
        return LabOrder::with(['clinic'])
            ->forPatient($patientId)
            ->where('status', 'completed')
            ->findOrFail($orderId);
    }
}

