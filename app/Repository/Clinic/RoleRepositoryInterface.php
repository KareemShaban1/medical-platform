<?php

namespace App\Repository\Clinic;

interface RoleRepositoryInterface
{
    public function index();
    public function data();
    public function create();
    public function store($request);
    public function show($id);
    public function edit($id);
    public function update($request, $id);
    public function destroy($id);
    public function trash();
    public function trashData();
    public function restore($id);
    public function forceDelete($id);
}
