<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $supplier = auth('supplier')->user()->supplier;
        $announcement = Announcement::active()
            ->where(function($q) use ($supplier){
                $q->where('target_suppliers_all', true)
                  ->orWhereHas('suppliers', function($q) use ($supplier){ $q->where('suppliers.id', $supplier->id); });
            })
            ->whereDoesntHave('dismissals', function($q) use ($supplier){
                $q->where('dismissable_type', \App\Models\Supplier::class)
                  ->where('dismissable_id', $supplier->id);
            })
            ->latest('created_at')
            ->first();

        return view('backend.dashboards.supplier.pages.dashboard', compact('announcement'));
    }
}
