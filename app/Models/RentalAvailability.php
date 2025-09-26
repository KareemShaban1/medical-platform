<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalAvailability extends Model
{
    //
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'rental_space_id',
        'type',
        'from_time',
        'to_time',
        'from_day',
        'to_day',
        'from_date',
        'to_date',
        'notes',
        'status',
    ];

    public function rentalSpace()
    {
        return $this->belongsTo(RentalSpace::class);
    }
}
