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
        return view('backend.dashboards.admin.pages.blog-posts.index');
    }

    public function data()
    {
        return $this->blogPostRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blogCategories = BlogCategory::select('id', 'name_en')->get();
        return view('backend.dashboards.admin.pages.blog-posts.create', compact('blogCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogPostRequest $request)
    {
        return $this->blogPostRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
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
        $blogPost = $this->blogPostRepo->show($id);
        $blogCategories = BlogCategory::select('id', 'name_en')->get();

        return view('backend.dashboards.admin.pages.blog-posts.edit', compact('blogPost', 'blogCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogPostRequest $request, $id)
    {
        return $this->blogPostRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->blogPostRepo->updateStatus($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->blogPostRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.admin.pages.blog-posts.trash');
    }

    public function trashData()
    {
        return $this->blogPostRepo->trashData();
    }

    public function restore($id)
    {
        return $this->blogPostRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->blogPostRepo->forceDelete($id);
    }
}
