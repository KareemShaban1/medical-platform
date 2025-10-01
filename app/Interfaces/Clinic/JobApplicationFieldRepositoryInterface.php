<?php


namespace App\Interfaces\Clinic;

interface JobApplicationFieldRepositoryInterface
{
    public function show($id);
    
    public function store($request);
    public function update($request, $id);
    public function updateStatus($request);

}