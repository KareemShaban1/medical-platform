<?php

namespace App\Interfaces\Admin;

interface BannerRepositoryInterface
{
    public function data();
    public function store($request);
    public function show($id);
    public function update($request, $id);
    public function destroy($id);
    public function trashData();
    public function restore($id);
    public function forceDelete($id);
    public function toggleStatus($id);
    public function incrementViews($id);
    public function incrementClicks($id);
}






