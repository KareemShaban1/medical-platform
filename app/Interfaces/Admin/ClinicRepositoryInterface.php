<?php

namespace App\Interfaces\Admin;

interface ClinicRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function clinicUsersData($id);
    public function store($request);
    public function update($request, $id);
    public function updateStatus($request);
    public function updateIsAllowed($request);
    public function destroy($id);
    public function showApproval($id);
}
