<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $categories = Category::query();

        return datatables()->of($categories)
            ->editColumn('status', fn($item) => $this->categoryStatus($item))
            ->addColumn('action', fn($item) => $this->categoryActions($item))
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveCategory(new Category(), $request, 'created');
    }

    public function show($id)
    {
        return Category::findOrFail($id);
    }

    public function update($request, $id)
    {
        $category = Category::findOrFail($id);
        return $this->saveCategory($category, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $category = Category::findOrFail($request->id);

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
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Category deleted successfully'),
        ]);
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveCategory($category, $request, string $action)
    {
        try {
            $category->fill($request->validated())->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Category '.$action.' successfully'),
                ]);
            }

            return redirect()->route('admin.categories.index')->with('success', __('Category '.$action.' successfully'));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function categoryStatus($item): string
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

    private function categoryActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editCategory({$item->id})" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></button>
            <button onclick="deleteCategory({$item->id})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
