<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalPricing extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'rental_space_id',
        'price',
        'notes',
    ];

    public function rentalSpace()
    {
        return $this->belongsTo(RentalSpace::class);
    }
}
