<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Clinic extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_allowed',
        'status',
    ];

    public $appends = ['images'];


    public function getImagesAttribute()
    {
        return $this->getMedia('clinic_images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    public function clinicUsers()
    {
        return $this->hasMany(ClinicUser::class);
    }

    public function approvement()
    {
        return $this->morphOne(ModuleApprovement::class, 'module');
    }

    public function scopeApproved($query)
    {
        return $query->whereHas('approvement', function ($query) {
            $query->where('action', 'approved');
        });
    }

    public function otps()
    {
        return $this->morphMany(UserOtp::class, 'otpable');
    }

    public function isVerified()
    {
        return $this->status == 1;
    }

}
