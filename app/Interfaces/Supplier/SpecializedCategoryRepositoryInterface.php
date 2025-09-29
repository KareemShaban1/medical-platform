<?php

namespace App\Interfaces\Supplier;

interface SpecializedCategoryRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function store($request);
    public function update($request, $id);
    public function destroy($id);
    public function getAvailableCategories();
    public function attachToCategory($categoryId);
}
