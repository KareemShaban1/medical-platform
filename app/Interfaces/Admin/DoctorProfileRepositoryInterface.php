<?php

namespace App\Interfaces\Admin;

interface DoctorProfileRepositoryInterface
{
    public function index();
    public function data();
    public function pendingData();
    public function show($id);
    public function approve($id);
    public function reject($id, $reason);
    public function toggleFeatured($id);
    public function toggleLockForEdit($id);
}
