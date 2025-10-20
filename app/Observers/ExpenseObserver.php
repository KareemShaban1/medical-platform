<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver
{
    //
    public function creating(Expense $expense)
    {
        if (!app()->runningInConsole()) {
            $expense->clinic_id = auth()->guard('clinic')->user()->clinic_id;
        }
    }
}
