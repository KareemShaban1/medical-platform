<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Interfaces\User\LabOrderRepositoryInterface;

class LabOrderController extends Controller
{
    public function __construct(private LabOrderRepositoryInterface $repo)
    {
    }

    public function index()
    {
        $patient = auth('patient')->user();
        $orders = $this->repo->listForPatient($patient->id);
        return view('frontend.patient.lab-orders.index', compact('orders', 'patient'));
    }

    public function show($id)
    {
        $patient = auth('patient')->user();
        $order = $this->repo->showForPatient($patient->id, $id);
        return view('frontend.patient.lab-orders.show', compact('order', 'patient'));
    }
}

