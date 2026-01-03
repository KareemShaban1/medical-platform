<?php

namespace App\Interfaces\Admin;

interface SupplierProductRepositoryInterface
{
    public function data();
    public function show($id);
    public function updateApprovalStatus(array $data, $id);
    public function supplierProducts($supplierId);
    public function destroy($id);
    public function trash();
    public function trashData();
    public function restore($id);
    public function forceDelete($id);
}
