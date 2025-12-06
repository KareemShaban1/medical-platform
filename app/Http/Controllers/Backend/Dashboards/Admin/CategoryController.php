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
        // apply permissions
        abort_if(!hasPermission('view categories'), 403, __('You are not authorized to view categories'));
        return view('backend.dashboards.admin.pages.categories.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view categories'), 403, __('You are not authorized to view categories'));
        return $this->categoryRepo->data();
    }

    public function store(StoreCategoryRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create category'), 403, __('You are not authorized to create category'));
        return $this->categoryRepo->store($request);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view categories'), 403, __('You are not authorized to view category'));
        $category = $this->categoryRepo->show($id);

        return request()->ajax()
            ? response()->json($category)
            : view('backend.dashboards.admin.pages.categories.show', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update category'), 403, __('You are not authorized to update category'));
        return $this->categoryRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('toggle category status'), 403, __('You are not authorized to update category status'));
        return $this->categoryRepo->updateStatus($request);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete category'), 403, __('You are not authorized to delete category'));
        return $this->categoryRepo->destroy($id);
    }


}
