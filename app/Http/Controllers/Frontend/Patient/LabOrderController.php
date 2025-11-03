<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Interfaces\User\LabOrderRepositoryInterface;
use Illuminate\Http\Request;

class LabOrderController extends Controller
{
    public function __construct(private LabOrderRepositoryInterface $repo)
    {
    }

    public function index(Request $request)
    {
        $patient = auth('patient')->user();
        $orders = $this->repo->listForPatient($patient->id, $request->input('from'), $request->input('to'));
        return view('frontend.patient.lab-orders.index', compact('orders', 'patient'));
    }

    public function show($id)
    {
        $patient = auth('patient')->user();
        $order = $this->repo->showForPatient($patient->id, $id);
        return view('frontend.patient.lab-orders.show', compact('order', 'patient'));
    }
}
