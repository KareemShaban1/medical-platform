<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategory extends Model
{
    /** @use HasFactory<\Database\Factories\BlogCategoryFactory> */
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug_en',
        'slug_ar',
        'status',
    ];

    protected $appends = ['name'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }

    // get name based on lang
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
}
