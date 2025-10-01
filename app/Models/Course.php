<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_ar',
        'slug_en',
        'slug_ar',
        'description_en',
        'description_ar',
        'level',
        'url',
        'duration',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
    ];

    // appends
    protected $appends = ['main_image', 'title'];

    // ------- attributes -------
    public function getMainImageAttribute()
    {
        return $this->getMedia('main_image')->first()?->getUrl() ?? null;
    }

    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getDescriptionAttribute()   
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }
    
    // ------- scopes -------
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    
}