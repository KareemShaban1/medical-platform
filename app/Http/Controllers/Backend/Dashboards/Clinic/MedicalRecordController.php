<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\MedicalRecord\UpdateMedicalRecordRequest;
use App\Models\Appointment;
use App\Interfaces\Clinic\MedicalRecordRepositoryInterface;
use App\Models\MedicalRecord;

class MedicalRecordController extends Controller
{
    public function __construct(protected MedicalRecordRepositoryInterface $repo)
    {
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view medical records'), 403, __('You are not authorized to view medical records'));

        return $this->repo->index();
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view medical records'), 403, __('You are not authorized to view medical records'));

        return $this->repo->data();
    }

    public function edit(Appointment $appointment)
    {
        // apply permissions
        abort_if(!hasPermission('update medical record'), 403, __('You are not authorized to update medical record'));

        return $this->repo->edit($appointment);
    }

    public function update(UpdateMedicalRecordRequest $request, Appointment $appointment)
    {
        // apply permissions
        abort_if(!hasPermission('update medical record'), 403, __('You are not authorized to update medical record'));

        return $this->repo->update($request, $appointment);
    }

    public function toggleShare(MedicalRecord $record)
    {
        // apply permissions
        abort_if(!hasPermission('share medical record'), 403, __('You are not authorized to share medical record'));

        return $this->repo->toggleShare($record);
    }
}