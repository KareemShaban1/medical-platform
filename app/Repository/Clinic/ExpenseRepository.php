<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Traits\HandlesMediaUploads;
use App\Models\Expense;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $expenses = Expense::query();

        return datatables()->of($expenses)
            ->addColumn('category', fn($item) => $item->category->name)
            ->addColumn('action', fn($item) => $this->expenseActions($item))
            ->rawColumns(['action', 'category', 'supplier'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveExpense(new Expense(), $request, 'created');
    }

    public function show($id)
    {
        return Expense::findOrFail($id);
    }

    public function update($request, $id)
    {
        $expense = Expense::findOrFail($id);
        return $this->saveExpense($expense, $request, 'updated');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return $this->jsonResponse('success', __('Expense deleted successfully'));
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $expenses = Expense::onlyTrashed()->get();

        return datatables()->of($expenses)
            ->addColumn('category', fn($item) => $item->category->name)
            ->addColumn('trash_action', fn($item) => $this->expenseTrashActions($item))
            ->rawColumns(['trash_action', 'category'])
            ->make(true);
    }

    public function restore($id)
    {
        $expense = Expense::onlyTrashed()->findOrFail($id);
        $expense->restore();

        return $this->jsonResponse('success', __('Expense restored successfully'));
    }

    public function forceDelete($id)
    {
        $expense = Expense::onlyTrashed()->findOrFail($id);
        $expense->forceDelete();


        return $this->jsonResponse('success', __('Expense deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveExpense($expense, $request, string $action)
    {
        try {
            DB::beginTransaction();
            $expense->fill($request->validated())->save();

            // Media
            if ($request->hasFile('images')) {
                $this->processMedia($expense, $request, [
                    ['field' => 'images', 'collection' => 'expense_images', 'multiple' => true],
                ], $action);
            }

            DB::commit();

            if ($request->ajax()) {
                return $this->jsonResponse('success', __('Expense ' . $action . ' successfully'));
            }

            return redirect()->route('clinic.expenses.index')->with('success', __('Expense ' . $action . ' successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function expenseActions($item): string
    {
        $editUrl = route('clinic.expenses.edit', $item->id);
        $showUrl = route('clinic.expenses.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
           <a href="{$editUrl}" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></a>
           <button onclick="deleteExpense({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }


    private function expenseTrashActions($item): string
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
