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
        return $this->repo->index();
    }

    public function data()
    {
        return $this->repo->data();
    }

    public function edit(Appointment $appointment)
    {
        return $this->repo->edit($appointment);
    }

    public function update(UpdateMedicalRecordRequest $request, Appointment $appointment)
    {
        return $this->repo->update($request, $appointment);
    }

    public function toggleShare(MedicalRecord $record)
    {
        return $this->repo->toggleShare($record);
    }
}
