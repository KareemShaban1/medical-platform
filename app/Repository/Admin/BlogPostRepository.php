<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\BlogPostRepositoryInterface;
use App\Models\BlogPost;
use App\Traits\HandlesMediaUploads;

class BlogPostRepository implements BlogPostRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $posts = BlogPost::query();

        return datatables()->of($posts)
            ->addColumn('category_name', fn($item) => $item->blogCategory->name)
            ->editColumn('status', fn($item) => $this->blogPostStatus($item))
            ->addColumn('action', fn($item) => $this->blogPostActions($item))
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->savePost(new BlogPost(), $request, 'created');
    }

    public function show($id)
    {
        return BlogPost::findOrFail($id);
    }


    public function update($request, $id)
    {
        $post = BlogPost::findOrFail($id);
        return $this->savePost($post, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $post = BlogPost::findOrFail($request->id);

        // fallback to "status" if field is not sent
        $field = $request->field ?? 'status';
        $value = (bool)$request->value;

        $post->{$field} = $value;
        $post->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Post status updated successfully'),
        ]);
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Blog Post deleted successfully'),
        ]);
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $posts = BlogPost::onlyTrashed()->get();

        return datatables()->of($posts)
            ->addColumn('category_name', fn($item) => $item->blogCategory->name)
            ->editColumn('status', fn($item) => $this->blogPostStatus($item))
            ->addColumn('trash_action', fn($item) => $this->blogPostTrashActions($item))
            ->rawColumns(['status', 'trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $post = BlogPost::onlyTrashed()->findOrFail($id);
        $post->restore();

        return $this->jsonResponse('success', __('Blog Post restored successfully'));
    }

    public function forceDelete($id)
    {
        $post = BlogPost::onlyTrashed()->findOrFail($id);
        $post->forceDelete();

        return $this->jsonResponse('success', __('Blog Post deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function savePost($post, $request, string $action)
    {
        try {
            $post->fill($request->validated())->save();

            // Media
            if ($request->hasFile('main_image')) {
                $this->processMedia($post, $request, [
                    ['field' => 'main_image', 'collection' => 'main_image', 'multiple' => false],
                ], $action);
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Blog Post '.$action.' successfully'),
                ]);
            }

            return redirect()->route('admin.blog-posts.index')->with('success', __('Blog Post '.$action.' successfully'));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function blogPostStatus($item): string
    {
        $checked = $item->status ? 'checked' : '';
        return <<<HTML
        <div class="form-check form-switch mt-2">
            <input type="checkbox" 
                   class="form-check-input toggle-boolean" 
                   data-id="{$item->id}" 
                   data-field="status" 
                   value="1" {$checked}>
        </div>
        HTML;
    }

    private function blogPostActions($item): string
    {
        $editUrl = route('admin.blog-posts.edit', $item->id);
        $showUrl = route('admin.blog-posts.show', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
           <a href="{$editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
           <button onclick="deleteBlogPost({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function blogPostTrashActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="restore({$item->id})" class="btn btn-sm btn-info" title="Restore"><i class="fa fa-undo"></i></button>
            <button onclick="forceDelete({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }


    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
