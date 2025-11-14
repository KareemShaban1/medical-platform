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
        'clinic_email',
        'clinic_website',
        'about',
        'services_offered',
        'working_hours',
        'has_emergency',
        'patient_rating',
    ];

    protected $casts = [
        'services_offered' => 'array',
        'working_hours' => 'array',
        'has_emergency' => 'boolean',
        'status' => 'boolean',
        'is_allowed' => 'boolean',
    ];

    public $appends = ['images', 'primary_image'];


    // ------- attributes -------
    public function getImagesAttribute()
    {
        $images = $this->getMedia('clinic_images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();

        // If no images, return default avatar
        if (empty($images)) {
            $images[] = 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=256&background=0D8ABC&color=fff';
        }

        return $images;
    }

    public function getPrimaryImageAttribute()
    {
        $media = $this->getMedia('clinic_images')->first();

        if ($media) {
            return $media->getUrl();
        }

        // Return default avatar if no images
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=256&background=0D8ABC&color=fff';
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
