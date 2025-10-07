<?php

namespace App\Observers;

use App\Models\ClinicInventory;

class ClinicInventoryObserver
{
    //
    public function creating(ClinicInventory $clinicInventory)
    {
        if (!app()->runningInConsole()) {
            $clinicInventory->clinic_id = auth()->guard('clinic')->user()->clinic_id;
        }
    }
}