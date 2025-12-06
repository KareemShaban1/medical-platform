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
        // apply permissions
        abort_if(!hasPermission('view invoices'), 403, __('You are not authorized to view invoices'));

        $clinicId = auth('clinic')->user()->clinic_id;
        $doctors = DoctorProfile::whereHas('clinicUser', fn($q) => $q->where('clinic_id', $clinicId))
            ->where('status', DoctorProfile::STATUS_APPROVED)
            ->get();
        $patients = Patient::registered()->forClinic($clinicId)->get();
        return view('backend.dashboards.clinic.pages.invoices.index', compact('doctors', 'patients'));
    }

    public function data(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view invoices'), 403, __('You are not authorized to view invoices'));

        return $this->repo->data($request->all());
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view invoices'), 403, __('You are not authorized to view invoice'));

        $invoice = $this->repo->show((int)$id);
        return view('backend.dashboards.clinic.pages.invoices.show', compact('invoice'));
    }

    public function updateHeader(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update invoice'), 403, __('You are not authorized to update invoice'));

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
        // apply permissions
        abort_if(!hasPermission('add invoice item'), 403, __('You are not authorized to add invoice item'));

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
        // apply permissions
        abort_if(!hasPermission('update invoice item'), 403, __('You are not authorized to update invoice item'));

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
        // apply permissions
        abort_if(!hasPermission('delete invoice item'), 403, __('You are not authorized to delete invoice item'));

        $this->repo->deleteItem((int)$id, (int)$itemId);
        return back()->with('success', __('Item removed'));
    }

    public function markPaid(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('mark invoice paid'), 403, __('You are not authorized to mark invoice paid'));

        $this->repo->markPaid((int)$id, $request->input('payment_method'));
        return back()->with('success', __('Invoice marked as paid'));
    }
}