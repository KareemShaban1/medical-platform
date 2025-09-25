<?php

namespace App\Interfaces\Admin;

interface AdminUserRepositoryInterface
{
    public function data();
    public function store(array $data);
    public function show($id);
    public function update(array $data, $id);
    public function destroy($id);
    public function trashData();
    public function restore($id);
    public function forceDelete($id);
    public function toggleStatus($id);
}
