<?php

namespace App\Models;

use App\Models\ExpenseCategory;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Clinic extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_allowed',
        'status',
        'governorate_id',
        'city_id',
        'area_id',
    ];

    public $appends = ['images'];


    // ------- attributes -------
    public function getImagesAttribute()
    {
        return $this->getMedia('clinic_images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    // ------- scopes -------
    public function scopeApproved($query)
    {
        return $query->whereHas('approvement', function ($query) {
            $query->where('action', 'approved');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // ------- relations -------
    public function clinicUsers()
    {
        return $this->hasMany(ClinicUser::class);
    }

    public function approvement()
    {
        return $this->morphOne(ModuleApprovement::class, 'module');
    }


    public function otps()
    {
        return $this->morphMany(UserOtp::class, 'otpable');
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function isVerified()
    {
        return $this->status == 1;
    }

    public function expenseCategories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }


}