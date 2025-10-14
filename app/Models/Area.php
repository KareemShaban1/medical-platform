<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    /** @use HasFactory<\Database\Factories\AreaFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'city_id',
    ];

    protected $appends = [
        'governorate_id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // custom attributes governorate_id
    public function getGovernorateIdAttribute()
    {
        return $this->city->governorate_id;
    }
}
