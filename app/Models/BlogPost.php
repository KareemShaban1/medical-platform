<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BlogPost extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\BlogPostFactory> */
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'blog_category_id',
        'title_en',
        'title_ar',
        'slug_en',
        'slug_ar',
        'content_en',
        'content_ar',
        'status',
    ];

    protected $appends = ['title', 'main_image'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class , 'blog_category_id', 'id');
    }

    public function getMainImageAttribute()
    {
        return $this->getMedia('main_image')->first()?->getUrl() ?? null;
    }

    // get title based on lang
    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }
}
