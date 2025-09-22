<?php

namespace App\Observers;

use App\Models\RentalSpace;

class RentalSpaceObserver
{
    //
    public function creating(RentalSpace $rentalSpace)
    {
        $rentalSpace->clinic_id = auth()->guard('clinic')->user()->clinic_id;
    }
    
}
