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

}