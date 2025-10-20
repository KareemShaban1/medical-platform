<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\LabOrderRepositoryInterface;
use App\Models\Patient;
use Illuminate\Http\Request;

class LabOrderController extends Controller
{
    public function __construct(private LabOrderRepositoryInterface $repo)
    {
    }

    public function index()
    {
        $patients = Patient::registered()->get();
        return view('backend.dashboards.clinic.pages.lab-orders.index', compact('patients'));
    }

    public function data()
    {
        return $this->repo->data();
    }

    public function create()
    {
        $patients = Patient::registered()->get();
        return view('backend.dashboards.clinic.pages.lab-orders.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'test_name' => 'required|string|max:255',
            'lab_name' => 'nullable|string|max:255',
            'cost_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'doctor_profile_id' => 'nullable|exists:doctor_profiles,id',
            'sent_at' => 'nullable|date',
        ]);

        $order = $this->repo->store($validated);

        return redirect()->route('clinic.lab-orders.show', $order->id)
            ->with('success', __('Lab order created successfully'));
    }

    public function show($id)
    {
        $order = $this->repo->show($id);
        return view('backend.dashboards.clinic.pages.lab-orders.show', compact('order'));
    }

    public function upload(Request $request, $id)
    {
        $validated = $request->validate([
            'results' => 'nullable|array',
            'results.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx|max:10240',
            'comment' => 'nullable|string',
            'replace' => 'nullable|boolean',
        ]);

        $this->repo->uploadResults(
            $id,
            $request->file('results'),
            $request->input('comment'),
            (bool)$request->input('replace')
        );

        return back()->with('success', __('Results updated successfully'));
    }

    public function complete($id)
    {
        $this->repo->complete($id);
        return back()->with('success', __('Lab order marked as completed'));
    }
}
