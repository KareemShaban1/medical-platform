<?php

namespace App\Interfaces\Admin;

interface SupplierProductRepositoryInterface
{
    public function data();
    public function show($id);
    public function updateApprovalStatus(array $data, $id);
    public function supplierProducts($supplierId);
}
