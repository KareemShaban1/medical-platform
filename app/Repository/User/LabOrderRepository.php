<?php

namespace App\Repository\User;

use App\Interfaces\User\LabOrderRepositoryInterface;
use App\Models\LabOrder;

class LabOrderRepository implements LabOrderRepositoryInterface
{
    public function listForPatient($patientId, ?string $from = null, ?string $to = null)
    {
        return LabOrder::with(['clinic'])
            ->forPatient($patientId)
            ->where('status', 'completed')
            ->when($from, function ($q) use ($from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('created_at', '<=', $to);
            })
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
