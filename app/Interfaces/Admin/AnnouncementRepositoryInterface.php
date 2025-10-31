<?php

namespace App\Interfaces\Admin;

interface AnnouncementRepositoryInterface
{
    public function data();
    public function store($request);
    public function show($id);
    public function update($request, $id);
    public function destroy($id);
}

