<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    /** @use HasFactory<\Database\Factories\CityFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'governorate_id',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
