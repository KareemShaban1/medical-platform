<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalPricing extends Model
{
    //
    use SoftDeletes;
    use HasFactory;
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
