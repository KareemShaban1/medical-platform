<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Models\BlogPost;
use App\Http\Requests\Admin\BlogPost\StoreBlogPostRequest;
use App\Http\Requests\Admin\BlogPost\UpdateBlogPostRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Admin\BlogPostRepositoryInterface;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    protected $blogPostRepo;

    public function __construct(BlogPostRepositoryInterface $blogPostRepo)
    {
        $this->blogPostRepo = $blogPostRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view blog posts'), 403, __('You are not authorized to view blog posts'));
        return view('backend.dashboards.admin.pages.blog-posts.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view blog posts'), 403, __('You are not authorized to view blog posts'));
        return $this->blogPostRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create blog post'), 403, __('You are not authorized to create blog post'));
        $blogCategories = BlogCategory::select('id', 'name_en')->get();
        return view('backend.dashboards.admin.pages.blog-posts.create', compact('blogCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogPostRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create blog post'), 403, __('You are not authorized to create blog post'));
        return $this->blogPostRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view blog posts'), 403, __('You are not authorized to view blog post'));
        $blogPost = $this->blogPostRepo->show($id);

        return request()->ajax()
            ? response()->json($blogPost)
            : view('backend.dashboards.admin.pages.blog-posts.show', compact('blogPost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update blog post'), 403, __('You are not authorized to update blog post'));
        $blogPost = $this->blogPostRepo->show($id);
        $blogCategories = BlogCategory::select('id', 'name_en')->get();

        return view('backend.dashboards.admin.pages.blog-posts.edit', compact('blogPost', 'blogCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogPostRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update blog post'), 403, __('You are not authorized to update blog post'));
        return $this->blogPostRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('update blog post status'), 403, __('You are not authorized to update blog post status'));
        return $this->blogPostRepo->updateStatus($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete blog post'), 403, __('You are not authorized to delete blog post'));
        return $this->blogPostRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash blog posts'), 403, __('You are not authorized to view trash blog posts'));
        return view('backend.dashboards.admin.pages.blog-posts.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash blog posts'), 403, __('You are not authorized to view trash blog posts'));
        return $this->blogPostRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore blog post'), 403, __('You are not authorized to restore blog post'));
        return $this->blogPostRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete blog post'), 403, __('You are not authorized to force delete blog post'));
        return $this->blogPostRepo->forceDelete($id);
    }
}