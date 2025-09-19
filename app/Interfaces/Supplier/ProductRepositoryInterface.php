<?php

namespace App\Interfaces\Supplier;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function store($request);
    public function update($request, $id);
    public function destroy($id);
    public function trash();
    public function trashData();
    public function restore($id);
    public function forceDelete($id);
}
