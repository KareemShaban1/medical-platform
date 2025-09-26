<?php

namespace App\Observers;

use App\Models\Job;

class JobObserver
{
    //
    public function creating(Job $job)
    {
        if (!app()->runningInConsole()) {
            $job->clinic_id = auth()->guard('clinic')->user()->clinic_id;
        }
    }
    
}