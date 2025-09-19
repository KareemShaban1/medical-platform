<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Interfaces\Admin\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.categories.index');
    }

    public function data()
    {
        return $this->categoryRepo->data();
    }

    public function store(StoreCategoryRequest $request)
    {
        return $this->categoryRepo->store($request);
    }

    public function show($id)
    {
        $category = $this->categoryRepo->show($id);

        return request()->ajax()
            ? response()->json($category)
            : view('backend.dashboards.admin.pages.categories.show', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        return $this->categoryRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->categoryRepo->updateStatus($request);
    }

    public function destroy($id)
    {
        return $this->categoryRepo->destroy($id);
    }

    
}