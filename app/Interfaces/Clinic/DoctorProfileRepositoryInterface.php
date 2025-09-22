<?php

namespace App\Interfaces\Clinic;

interface DoctorProfileRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function store($request);
    public function update($request, $id);
    public function destroy($id);
    public function getUserProfile($clinicUserId);
    public function submitForReview($id);
}
