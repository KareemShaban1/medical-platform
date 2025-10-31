<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $clinic = auth('clinic')->user()->clinic;
        $announcement = Announcement::active()
            ->where(function($q) use ($clinic){
                $q->where('target_clinics_all', true)
                  ->orWhereHas('clinics', function($q) use ($clinic){ $q->where('clinics.id', $clinic->id); });
            })
            ->whereDoesntHave('dismissals', function($q) use ($clinic){
                $q->where('dismissable_type', \App\Models\Clinic::class)
                  ->where('dismissable_id', $clinic->id);
            })
            ->latest('created_at')
            ->first();


        return view('backend.dashboards.clinic.pages.dashboard', compact('announcement'));
    }
}
