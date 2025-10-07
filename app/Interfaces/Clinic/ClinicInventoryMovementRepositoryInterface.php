<?php

namespace App\Interfaces\Clinic;

interface ClinicInventoryMovementRepositoryInterface
{
    public function index();
    public function data($id);
    public function show($id);
    public function store($request);
    public function update($request, $id);
    public function destroy($id);
    public function trash();
    public function trashData();
    public function restore($id);
    public function forceDelete($id);
}