<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\SupplierUser;

class Supplier extends Model implements HasMedia
{
    use InteractsWithMedia;

    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_allowed',
        'status',
    ];

    public $appends = ['images'];


    // attributes
    public function getImagesAttribute()
    {
        return $this->getMedia('supplier_images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    // scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeApproved($query)
    {
        return $query->whereHas('approvement', function ($query) {
            $query->where('action', 'approved');
        });
    }


    // relationships

    public function supplierUsers()
    {
        return $this->hasMany(SupplierUser::class);
    }

    public function approvement()
    {
        return $this->morphOne(ModuleApprovement::class, 'module');
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
