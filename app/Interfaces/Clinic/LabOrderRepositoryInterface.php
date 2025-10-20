<?php

namespace App\Interfaces\Clinic;

interface LabOrderRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function store($request);
    public function uploadResults($id, $files = null, ?string $comment = null, bool $replace = false);
    public function complete($id);
}
