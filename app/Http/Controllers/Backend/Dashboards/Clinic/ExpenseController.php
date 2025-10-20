<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Http\Requests\Clinic\Expense\StoreExpenseRequest;
use App\Http\Requests\Clinic\Expense\UpdateExpenseRequest;
use App\Interfaces\Clinic\ExpenseRepositoryInterface;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
class ExpenseController extends Controller
{
    protected $expenseRepo;

    public function __construct(ExpenseRepositoryInterface $expenseRepo)
    {
        $this->expenseRepo = $expenseRepo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.dashboards.clinic.pages.expenses.index');
    }

    public function data()
    {
        return $this->expenseRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $expenseCategories = ExpenseCategory::all();
        $suppliers = Supplier::all();
        return view('backend.dashboards.clinic.pages.expenses.create', compact('expenseCategories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        return $this->expenseRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expense = $this->expenseRepo->show($id);
        return request()->ajax()
            ? response()->json($expense)
            : view('backend.dashboards.clinic.pages.expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = $this->expenseRepo->show($id);
        $expenseCategories = ExpenseCategory::all();
        $suppliers = Supplier::all();
        return view('backend.dashboards.clinic.pages.expenses.edit', compact('expense', 'expenseCategories', 'suppliers'));
    }


    public function update(UpdateExpenseRequest $request, $id)
    {
        return $this->expenseRepo->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->expenseRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.expenses.trash');
    }

    public function trashData()
    {
        return $this->expenseRepo->trashData();
    }

    public function restore($id)
    {
        return $this->expenseRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->expenseRepo->forceDelete($id);
    }
}