<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\CreateSpecializedCategoryRequest;
use App\Http\Requests\Supplier\UpdateSpecializedCategoryRequest;
use App\Interfaces\Supplier\SpecializedCategoryRepositoryInterface;

class SpecializedCategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(SpecializedCategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view specialized categories'), 403, __('You are not authorized to view specialized categories'));

        return view('backend.dashboards.supplier.pages.specialized-categories.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view specialized categories'), 403, __('You are not authorized to view specialized categories'));

        return $this->categoryRepository->data();
    }

    public function store(CreateSpecializedCategoryRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create specialized category'), 403, __('You are not authorized to create specialized categories'));

        try {
            $this->categoryRepository->store($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Specialized category created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view specialized category'), 403, __('You are not authorized to view specialized category'));

        try {
            $category = $this->categoryRepository->show($id);
            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(UpdateSpecializedCategoryRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update specialized category'), 403, __('You are not authorized to update specialized category'));

        try {
            $this->categoryRepository->update($request->validated(), $id);
            return response()->json([
                'success' => true,
                'message' => 'Specialized category updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete specialized category'), 403, __('You are not authorized to delete specialized category'));

        try {
            $this->categoryRepository->destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Specialization removed successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getAvailableCategories()
    {
        try {
            $categories = $this->categoryRepository->getAvailableCategories();
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function attachToCategory()
    {
        try {
            $categoryId = request('category_id');
            $this->categoryRepository->attachToCategory($categoryId);
            return response()->json([
                'success' => true,
                'message' => 'Successfully specialized in this category.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
