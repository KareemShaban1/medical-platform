<?php

namespace App\Repository\Admin;

interface CategoryRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function store($request);
    public function update($request, $id);
    public function updateStatus($request);
    public function destroy($id);
    public function trash();

    public function trashData();

    public function restore($id);
    public function forceDelete($id);
}