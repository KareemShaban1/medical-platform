<?php

namespace App\Interfaces\Clinic;

use App\Http\Requests\Clinic\MedicalRecord\UpdateMedicalRecordRequest;
use App\Models\Appointment;
use App\Models\MedicalRecord;

interface MedicalRecordRepositoryInterface
{
    public function index();
    public function data();
    public function edit(Appointment $appointment);
    public function update(UpdateMedicalRecordRequest $request, Appointment $appointment);
    public function toggleShare(MedicalRecord $record);
}
