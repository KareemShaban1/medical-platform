<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\ExpenseCategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Traits\HandlesMediaUploads;
use App\Models\ExpenseCategory;

class ExpenseCategoryRepository implements ExpenseCategoryRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $expenseCategories = ExpenseCategory::query();

        return datatables()->of($expenseCategories)
            ->addColumn('action', fn($item) => $this->expenseCategoryActions($item))
            ->editColumn('status', fn($item) => $this->expenseCategoryStatus($item))
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveExpenseCategory(new ExpenseCategory(), $request, 'created');
    }

    public function show($id)
    {
        return ExpenseCategory::findOrFail($id);
    }

    public function update($request, $id)
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);
        return $this->saveExpenseCategory($expenseCategory, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $expenseCategory = ExpenseCategory::findOrFail($request->id);

        // fallback to "status" if field is not sent
        $field = $request->field ?? 'status';
        $value = (bool)$request->value;

        $expenseCategory->{$field} = $value;
        $expenseCategory->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Expense category status updated successfully'),
        ]);
    }

    public function destroy($id)
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);
        $expenseCategory->delete();

        return $this->jsonResponse('success', __('Expense category deleted successfully'));
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $expenseCategories = ExpenseCategory::onlyTrashed()->get();

        return datatables()->of($expenseCategories)
            ->addColumn('trash_action', fn($item) => $this->expenseCategoryTrashActions($item))
            ->rawColumns(['trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $expenseCategory = ExpenseCategory::onlyTrashed()->findOrFail($id);
        $expenseCategory->restore();

        return $this->jsonResponse('success', __('Expense category restored successfully'));
    }

    public function forceDelete($id)
    {
        $expenseCategory = ExpenseCategory::onlyTrashed()->findOrFail($id);
        $expenseCategory->forceDelete();


        return $this->jsonResponse('success', __('Expense category deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveExpenseCategory($expenseCategory, $request, string $action)
    {
        try {
            DB::beginTransaction();

            $expenseCategory->fill($request->validated());
            $expenseCategory->save();

            DB::commit();

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Expense category ' . $action . ' successfully'));
            }

            return redirect()
                ->route('clinic.expense-categories.index')
                ->with('success', __('Expense category ' . $action . ' successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse('error', $e->getMessage());
        }
    }


    private function expenseCategoryActions($item): string
    {
        $editUrl = route('clinic.expense-categories.edit', $item->id);
        $showUrl = route('clinic.expense-categories.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
           <button onclick="editExpenseCategory({$item->id})" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></button>
           <button onclick="deleteExpenseCategory({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }


    private function expenseCategoryTrashActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
           <button onclick="restore({$item->id})" class="btn btn-sm btn-info" title="Restore"><i class="fa fa-undo"></i></button>
           <button onclick="forceDelete({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function expenseCategoryStatus($item): string
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


    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
    }