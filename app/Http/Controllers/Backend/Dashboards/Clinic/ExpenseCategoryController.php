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
        return view('backend.dashboards.clinic.pages.expense-categories.index');
    }

    public function data()
    {
        return $this->expenseCategoryRepo->data();
    }

            /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dashboards.clinic.pages.expense-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseCategoryRequest $request)
    {
        return $this->expenseCategoryRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
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
        $expenseCategory = $this->expenseCategoryRepo->show($id);
        return view('backend.dashboards.clinic.pages.expense-categories.edit', compact('expenseCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseCategoryRequest $request, $id)
    {
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
        return $this->expenseCategoryRepo->destroy($id);
    }
    public function trash()
    {
        return view('backend.dashboards.clinic.pages.expense-categories.trash');
    }
    public function trashData()
    {
        return $this->expenseCategoryRepo->trashData();
    }
    public function restore($id)
    {
        return $this->expenseCategoryRepo->restore($id);
    }
    public function forceDelete($id)
    {
        return $this->expenseCategoryRepo->forceDelete($id);
    }
}
