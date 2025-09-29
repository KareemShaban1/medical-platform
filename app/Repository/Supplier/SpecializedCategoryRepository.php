<?php

namespace App\Repository\Supplier;

use App\Interfaces\Supplier\SpecializedCategoryRepositoryInterface;
use App\Models\SupplierSpecializedCategory;
use Illuminate\Support\Facades\DB;

class SpecializedCategoryRepository implements SpecializedCategoryRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $supplierId = auth('supplier')->user()->supplier_id;
        $categories = SupplierSpecializedCategory::whereHas('suppliers', function($q) use ($supplierId) {
            $q->where('suppliers.id', $supplierId);
        })->latest();

        return datatables()->of($categories)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('name_en', fn($item) => $item->name_en)
            ->addColumn('name_ar', fn($item) => $item->name_ar)
            ->addColumn('requests_count', fn($item) => $item->requests()->count())
            ->addColumn('suppliers_count', fn($item) => $item->suppliers()->count())
            ->addColumn('action', fn($item) => $this->categoryActions($item))
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;
            $supplierId = auth('supplier')->user()->supplier_id;

            // Create new category or get existing one
            $category = SupplierSpecializedCategory::firstOrCreate([
                'name_en' => $data['name_en'],
                'name_ar' => $data['name_ar'],
            ]);

            // Attach supplier to category if not already attached
            if (!$category->suppliers()->where('supplier_id', $supplierId)->exists()) {
                $category->suppliers()->attach($supplierId);
            }

            return $category;
        });
    }

    public function show($id)
    {
        $supplierId = auth('supplier')->user()->supplier_id;
        return SupplierSpecializedCategory::whereHas('suppliers', function($q) use ($supplierId) {
            $q->where('suppliers.id', $supplierId);
        })->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $supplierId = auth('supplier')->user()->supplier_id;
            $category = SupplierSpecializedCategory::whereHas('suppliers', function($q) use ($supplierId) {
                $q->where('suppliers.id', $supplierId);
            })->findOrFail($id);

            $data = $request;
            $category->update($data);

            return $category;
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $supplierId = auth('supplier')->user()->supplier_id;
            $category = SupplierSpecializedCategory::whereHas('suppliers', function($q) use ($supplierId) {
                $q->where('suppliers.id', $supplierId);
            })->findOrFail($id);

            // Check if there are any active requests in this category
            if ($category->requests()->where('status', 'open')->exists()) {
                throw new \Exception('Cannot remove specialization while there are active requests in this category');
            }

            // Just detach the supplier from this category, don't delete the category
            $category->suppliers()->detach($supplierId);
            return $category;
        });
    }

    public function getAvailableCategories()
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        // Get categories that this supplier is NOT specialized in
        return SupplierSpecializedCategory::whereDoesntHave('suppliers', function($q) use ($supplierId) {
            $q->where('suppliers.id', $supplierId);
        })->get();
    }

    public function attachToCategory($categoryId)
    {
        return DB::transaction(function () use ($categoryId) {
            $supplierId = auth('supplier')->user()->supplier_id;
            $category = SupplierSpecializedCategory::findOrFail($categoryId);

            // Check if already attached
            if ($category->suppliers()->where('supplier_id', $supplierId)->exists()) {
                throw new \Exception('You are already specialized in this category');
            }

            $category->suppliers()->attach($supplierId);
            return $category;
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function categoryActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editCategory({$item->id})" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>
            <button onclick="deleteCategory({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }
}
