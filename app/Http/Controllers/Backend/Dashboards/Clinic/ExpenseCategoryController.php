<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\ExpenseCategoryRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Clinic\Expense\StoreExpenseCategoryRequest;
use App\Http\Requests\Clinic\Expense\UpdateExpenseCategoryRequest;
class ExpenseCategoryController extends Controller
{
    protected $expenseCategoryRepo;

    public function __construct(ExpenseCategoryRepositoryInterface $expenseCategoryRepo)
    {
        $this->expenseCategoryRepo = $expenseCategoryRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view expense categories'), 403, __('You are not authorized to view expense categories'));

        return view('backend.dashboards.clinic.pages.expense-categories.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view expense categories'), 403, __('You are not authorized to view expense categories'));

        return $this->expenseCategoryRepo->data();
    }

            /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create expense category'), 403, __('You are not authorized to create expense category'));

        return view('backend.dashboards.clinic.pages.expense-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseCategoryRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create expense category'), 403, __('You are not authorized to create expense category'));

        return $this->expenseCategoryRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view expense categories'), 403, __('You are not authorized to view expense category'));

        $expenseCategory = $this->expenseCategoryRepo->show($id);
        return request()->ajax()
            ? response()->json($expenseCategory)
            : view('backend.dashboards.clinic.pages.expense-categories.show', compact('expenseCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update expense category'), 403, __('You are not authorized to update expense category'));

        $expenseCategory = $this->expenseCategoryRepo->show($id);
        return view('backend.dashboards.clinic.pages.expense-categories.edit', compact('expenseCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseCategoryRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update expense category'), 403, __('You are not authorized to update expense category'));

        return $this->expenseCategoryRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->expenseCategoryRepo->updateStatus($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete expense category'), 403, __('You are not authorized to delete expense category'));

        return $this->expenseCategoryRepo->destroy($id);
    }
    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash expense categories'), 403, __('You are not authorized to view trash expense categories'));

        return view('backend.dashboards.clinic.pages.expense-categories.trash');
    }
    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash expense categories'), 403, __('You are not authorized to view trash expense categories'));

        return $this->expenseCategoryRepo->trashData();
    }
    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore expense category'), 403, __('You are not authorized to restore expense category'));

        return $this->expenseCategoryRepo->restore($id);
    }
    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete expense category'), 403, __('You are not authorized to force delete expense category'));

        return $this->expenseCategoryRepo->forceDelete($id);
    }
}