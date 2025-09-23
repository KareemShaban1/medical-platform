<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\BlogCategoryRepositoryInterface;
use App\Models\BlogCategory;

class BlogCategoryRepository implements BlogCategoryRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $categories = BlogCategory::query();

        return datatables()->of($categories)
            ->editColumn('status', fn($item) => $this->blogCategoryStatus($item))
            ->addColumn('action', fn($item) => $this->blogCategoryActions($item))
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveCategory(new BlogCategory(), $request, 'created');
    }

    public function show($id)
    {
        return BlogCategory::findOrFail($id);
    }

    public function update($request, $id)
    {
        $category = BlogCategory::findOrFail($id);
        return $this->saveCategory($category, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $category = BlogCategory::findOrFail($request->id);

        // fallback to "status" if field is not sent
        $field = $request->field ?? 'status';
        $value = (bool)$request->value;

        $category->{$field} = $value;
        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Category status updated successfully'),
        ]);
    }

    public function destroy($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Blog Category deleted successfully'),
        ]);
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $categories = BlogCategory::onlyTrashed()->get();

        return datatables()->of($categories)
            ->editColumn('status', fn($item) => $this->blogCategoryStatus($item))
            ->addColumn('trash_action', fn($item) => $this->blogCategoryTrashActions($item))
            ->rawColumns(['status', 'trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $category = BlogCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        return $this->jsonResponse('success', __('Blog Category restored successfully'));
    }

    public function forceDelete($id)
    {
        $category = BlogCategory::onlyTrashed()->findOrFail($id);
        $category->forceDelete();

        return $this->jsonResponse('success', __('Blog Category deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveCategory($category, $request, string $action)
    {
        try {
            $category->fill($request->validated())->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Blog Category '.$action.' successfully'),
                ]);
            }

            return redirect()->route('admin.blog-categories.index')->with('success', __('Blog Category '.$action.' successfully'));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function blogCategoryStatus($item): string
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

    private function blogCategoryActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editBlogCategory({$item->id})" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></button>
            <button onclick="deleteBlogCategory({$item->id})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function blogCategoryTrashActions($item): string
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
