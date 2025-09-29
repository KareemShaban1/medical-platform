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
        return view('backend.dashboards.supplier.pages.specialized-categories.index');
    }

    public function data()
    {
        return $this->categoryRepository->data();
    }

    public function store(CreateSpecializedCategoryRequest $request)
    {
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
