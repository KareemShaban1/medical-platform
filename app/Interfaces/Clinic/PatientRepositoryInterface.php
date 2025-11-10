<?php

namespace App\Interfaces\Clinic;

interface PatientRepositoryInterface
{
    public function index();
    public function data();
    public function trashData();
    public function store($request);
    public function show($id);
    public function edit($request, $id);
    public function update($request, $id);
    public function destroy($id);
    public function restore($patientId);
    public function forceDelete($patientId);
}
