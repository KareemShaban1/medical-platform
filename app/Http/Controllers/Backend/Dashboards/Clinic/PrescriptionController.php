<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Http\Requests\Clinic\Prescription\StorePrescriptionRequest;
use App\Http\Requests\Clinic\Prescription\UpdatePrescriptionRequest;
use App\Interfaces\Clinic\PrescriptionRepositoryInterface;
use App\Interfaces\Clinic\AppointmentRepositoryInterface;
use App\Models\Appointment;
use Illuminate\Http\Request;


class PrescriptionController extends Controller
{
    protected $prescriptionRepo;
    protected $appointmentRepo;

    public function __construct(
        PrescriptionRepositoryInterface $prescriptionRepo,
        AppointmentRepositoryInterface $appointmentRepo
    ) {
        $this->prescriptionRepo = $prescriptionRepo;
        $this->appointmentRepo = $appointmentRepo;
    }

    public function index()
    {
        return view('backend.dashboards.clinic.pages.prescriptions.index');
    }

    public function data()
    {
        return $this->prescriptionRepo->data();
    }

    // create
    public function create($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        return view('backend.dashboards.clinic.pages.prescriptions.create', compact('appointment'));
    }

    public function store(StorePrescriptionRequest $request)
    {
        return $this->prescriptionRepo->store($request);
    }

    public function show($id)
    {
        $prescription = $this->prescriptionRepo->show($id);

        return request()->ajax()
            ? response()->json($prescription)
            : view('backend.dashboards.clinic.pages.prescriptions.show', compact('prescription'));
    }

    public function edit($id){
        $prescription = $this->prescriptionRepo->show($id);

        return view('backend.dashboards.clinic.pages.prescriptions.edit', compact('prescription'));

    }

    public function update(UpdatePrescriptionRequest $request, $id)
    {
        return $this->prescriptionRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->prescriptionRepo->updateStatus($request);
    }

    public function destroy($id)
    {
        return $this->prescriptionRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.prescriptions.trash');
    }

    public function trashData()
    {
        return $this->prescriptionRepo->trashData();
    }


    public function restore($id)
    {
        return $this->prescriptionRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->prescriptionRepo->forceDelete($id);
    }

    /**
     * Generate prescription print view for an appointment
     *
     * @param int $appointmentId
     * @param Request $request
     * @return \Illuminate\View\View|\Barryvdh\DomPDF\PDF
     */
    public function print($appointmentId, Request $request)
    {
        $asPdf = $request->has('pdf') || $request->get('format') === 'pdf';
        return $this->appointmentRepo->generatePrescription($appointmentId, $asPdf);
    }

    /**
     * Download prescription as PDF
     *
     * @param int $appointmentId
     * @return \Barryvdh\DomPDF\PDF
     */
    public function downloadPdf($appointmentId)
    {
        return $this->appointmentRepo->generatePrescription($appointmentId, true);
    }
}
