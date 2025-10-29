<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
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

    public function analytics(Request $request)
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $expenses = Expense::where('clinic_id', $clinicId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->with(['category', 'supplier'])
            ->get();

        $analytics = [
            'total_records' => $expenses->count(),
            'total_amount' => (float) $expenses->sum('amount'),
            'average_amount' => $expenses->count() > 0 ? (float) $expenses->avg('amount') : 0,

            // Sum by date for trend
            'amount_by_date' => $expenses->groupBy(function ($e) {
                return \Carbon\Carbon::parse($e->expense_date)->format('Y-m-d');
            })->map(function ($group) {
                return (float) $group->sum('amount');
            }),

            // Category breakdown by amount
            'amount_by_category' => $expenses->groupBy(function ($e) {
                return $e->category?->name ?? __('Uncategorized');
            })->map(function ($group) {
                return (float) $group->sum('amount');
            })->sortDesc(),

            // Top category name
            'top_category' => (function () use ($expenses) {
                $byCat = $expenses->groupBy(fn($e) => $e->category?->name ?? __('Uncategorized'))
                    ->map(fn($g) => (float) $g->sum('amount'))
                    ->sortDesc();
                return $byCat->keys()->first();
            })(),

            // Top supplier total and name (optional)
            'top_supplier' => (function () use ($expenses) {
                $bySup = $expenses->groupBy(fn($e) => $e->supplier?->name ?? __('Unknown'))
                    ->map(fn($g) => (float) $g->sum('amount'))
                    ->sortDesc();
                return [
                    'name' => $bySup->keys()->first(),
                    'amount' => $bySup->values()->first() ?? 0,
                ];
            })(),

            // Recent
            'recent_expenses' => $expenses->sortByDesc('expense_date')->take(5),
        ];

        return view('backend.dashboards.clinic.pages.expenses.analytics', compact(
            'analytics',
            'expenses',
            'startDate',
            'endDate'
        ));
    }
}
