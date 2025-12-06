<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\LabOrderRepositoryInterface;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Traits\HandlesFeatureLimits;
use Illuminate\Support\Facades\Auth;

class LabOrderController extends Controller
{
    use HandlesFeatureLimits;

    public function __construct(private LabOrderRepositoryInterface $repo)
    {
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view lab orders'), 403, __('You are not authorized to view lab orders'));

        $clinicId = auth('clinic')->user()->clinic_id;
        $patients = Patient::registered()->forClinic($clinicId)->get();
        return view('backend.dashboards.clinic.pages.lab-orders.index', compact('patients'));
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view lab orders'), 403, __('You are not authorized to view lab orders'));

        return $this->repo->data();
    }

    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create lab order'), 403, __('You are not authorized to create lab orders'));

        $clinicId = auth('clinic')->user()->clinic_id;
        $patients = Patient::registered()->forClinic($clinicId)->get();
        return view('backend.dashboards.clinic.pages.lab-orders.create', compact('patients'));
    }

    public function store(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('create lab order'), 403, __('You are not authorized to create lab orders'));

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'test_name' => 'required|string|max:255',
            'lab_name' => 'nullable|string|max:255',
            'cost_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'doctor_profile_id' => 'nullable|exists:doctor_profiles,id',
            'sent_at' => 'nullable|date',
        ]);

        $clinic = Auth::guard('clinic')->user()->clinic_id
            ? Auth::guard('clinic')->user()->clinic
            : Auth::guard('clinic')->user();

        $order = $this->checkFeatureLimit(
            $clinic,
            'lab_module',
            function () use ($validated) {
                return $this->repo->store($validated);
            }
        );

        return redirect()->route('clinic.lab-orders.show', $order->id)
            ->with('success', __('Lab order created successfully'));
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view lab orders'), 403, __('You are not authorized to view lab order'));

        $order = $this->repo->show($id);
        return view('backend.dashboards.clinic.pages.lab-orders.show', compact('order'));
    }

    public function upload(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('upload lab order'), 403, __('You are not authorized to upload lab order'));

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
        // apply permissions
        abort_if(!hasPermission('complete lab order'), 403, __('You are not authorized to complete lab order'));

        $this->repo->complete($id);
        return back()->with('success', __('Lab order marked as completed'));
    }
}