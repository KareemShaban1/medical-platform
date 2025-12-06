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
        // apply permissions
        abort_if(!hasPermission('view blog categories'), 403, __('You are not authorized to view blog categories'));
        return view('backend.dashboards.admin.pages.blog-categories.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view blog categories'), 403, __('You are not authorized to view blog categories'));
        return $this->blogCategoryRepo->data();
    }

    public function store(StoreBlogCategoryRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create blog category'), 403, __('You are not authorized to create blog category'));
        return $this->blogCategoryRepo->store($request);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view blog categories'), 403, __('You are not authorized to view blog category'));
        $category = $this->blogCategoryRepo->show($id);

        return request()->ajax()
            ? response()->json($category)
            : view('backend.dashboards.admin.pages.blog-categories.show', compact('category'));
    }

    public function update(UpdateBlogCategoryRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update blog category'), 403, __('You are not authorized to update blog category'));
        return $this->blogCategoryRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('update blog category status'), 403, __('You are not authorized to update blog category status'));
        return $this->blogCategoryRepo->updateStatus($request);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete blog category'), 403, __('You are not authorized to delete blog category'));
        return $this->blogCategoryRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash blog categories'), 403, __('You are not authorized to view trash blog categories'));
        return view('backend.dashboards.admin.pages.blog-categories.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash blog categories'), 403, __('You are not authorized to view trash blog categories'));
        return $this->blogCategoryRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore blog category'), 403, __('You are not authorized to restore blog category'));
        return $this->blogCategoryRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete blog category'), 403, __('You are not authorized to force delete blog category'));
        return $this->blogCategoryRepo->forceDelete($id);
    }
}