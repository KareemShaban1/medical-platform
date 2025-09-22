<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalBooking extends Model
{
    
    //
    use SoftDeletes;
    protected $fillable = [
        'rental_space_id',
        'booking_user_id',
        'clinic_id',
        'price',
        'notes',
    ];

    public function rentalSpace()
    {
        return $this->belongsTo(RentalSpace::class);
    }

    public function bookingUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
