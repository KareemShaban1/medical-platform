<?php

namespace App\Observers;

use App\Models\ExpenseCategory;

class ExpenseCategoryObserver
{
    //
    public function creating(ExpenseCategory $expenseCategory)
    {
        if (!app()->runningInConsole()) {
            $expenseCategory->clinic_id = auth()->guard('clinic')->user()->clinic_id;
        }
    }
}
