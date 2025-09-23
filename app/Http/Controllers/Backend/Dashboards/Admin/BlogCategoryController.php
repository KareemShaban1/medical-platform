<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Models\BlogCategory;
use App\Http\Requests\Admin\BlogCategory\StoreBlogCategoryRequest;
use App\Http\Requests\Admin\BlogCategory\UpdateBlogCategoryRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Admin\BlogCategoryRepositoryInterface;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    protected $blogCategoryRepo;

    public function __construct(BlogCategoryRepositoryInterface $blogCategoryRepo)
    {
        $this->blogCategoryRepo = $blogCategoryRepo;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.blog-categories.index');
    }

    public function data()
    {
        return $this->blogCategoryRepo->data();
    }

    public function store(StoreBlogCategoryRequest $request)
    {
        return $this->blogCategoryRepo->store($request);
    }

    public function show($id)
    {
        $category = $this->blogCategoryRepo->show($id);

        return request()->ajax()
            ? response()->json($category)
            : view('backend.dashboards.admin.pages.blog-categories.show', compact('category'));
    }

    public function update(UpdateBlogCategoryRequest $request, $id)
    {
        return $this->blogCategoryRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->blogCategoryRepo->updateStatus($request);
    }

    public function destroy($id)
    {
        return $this->blogCategoryRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.admin.pages.blog-categories.trash');
    }

    public function trashData()
    {
        return $this->blogCategoryRepo->trashData();
    }

    public function restore($id)
    {
        return $this->blogCategoryRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->blogCategoryRepo->forceDelete($id);
    }
}
