<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Job extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\JobFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'clinic_jobs';

    protected $fillable = [
        'title',
        'type',
        'description',
        'location',
        'salary',
        'status',
        'clinic_id',
    ];

    public $appends = ['main_image'];

    public function getMainImageAttribute()
    {
        $mainImage = $this->getMedia('main_image')->first();
        return $mainImage ? $mainImage->getUrl() : 'https://placehold.co/350x263';
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

    public function approvement()
    {
        return $this->morphOne(ModuleApprovement::class, 'module');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
