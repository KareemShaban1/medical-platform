<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalSpace extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    //
    protected $fillable = [
        'clinic_id',
        'name',
        'location',
        'description',
        'status',
    ];

    public $appends = ['main_image', 'images'];

    public function scopeApproved($query)
    {
        return $query->whereHas('approvement', function ($query) {
            $query->where('action', 'approved');
        });
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function approvement()
    {
        return $this->morphOne(ModuleApprovement::class, 'module');
    }

    public function availability()
    {
        return $this->hasOne(RentalAvailability::class);
    }

    public function pricing()
    {
        return $this->hasOne(RentalPricing::class);
    }

    public function booking()
    {
        return $this->hasMany(RentalBooking::class);
    }

    public function getMainImageAttribute()
    {
        return $this->getMedia('main_image')->first()?->getUrl() ?? null;
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('rental_space_images')->map(function ($media) {
            return $media?->getUrl() ?? null;
        })->toArray();
    }
}
