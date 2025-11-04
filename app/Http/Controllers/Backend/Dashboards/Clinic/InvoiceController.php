<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\InvoiceRepositoryInterface;
use App\Models\DoctorProfile;
use App\Models\Patient;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceRepositoryInterface $repo)
    {
    }

    public function index()
    {
        $clinicId = auth('clinic')->user()->clinic_id;
        $doctors = DoctorProfile::whereHas('clinicUser', fn($q) => $q->where('clinic_id', $clinicId))
            ->where('status', DoctorProfile::STATUS_APPROVED)
            ->get();
        $patients = Patient::registered()->forClinic($clinicId)->get();
        return view('backend.dashboards.clinic.pages.invoices.index', compact('doctors', 'patients'));
    }

    public function data(Request $request)
    {
        return $this->repo->data($request->all());
    }

    public function show($id)
    {
        $invoice = $this->repo->show((int)$id);
        return view('backend.dashboards.clinic.pages.invoices.show', compact('invoice'));
    }

    public function updateHeader(Request $request, $id)
    {
        $validated = $request->validate([
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
        ]);
        $invoice = $this->repo->updateHeader((int)$id, $validated);
        return back()->with('success', __('Invoice updated'));
    }

    public function addItem(Request $request, $id)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);
        $this->repo->addItem((int)$id, $validated);
        return back()->with('success', __('Item added'));
    }

    public function updateItem(Request $request, $id, $itemId)
    {
        $validated = $request->validate([
            'description' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|nullable|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
        ]);
        $this->repo->updateItem((int)$id, (int)$itemId, $validated);
        return back()->with('success', __('Item updated'));
    }

    public function deleteItem($id, $itemId)
    {
        $this->repo->deleteItem((int)$id, (int)$itemId);
        return back()->with('success', __('Item removed'));
    }

    public function markPaid(Request $request, $id)
    {
        $this->repo->markPaid((int)$id, $request->input('payment_method'));
        return back()->with('success', __('Invoice marked as paid'));
    }
}

