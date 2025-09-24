<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name_ar',
        'name_en',
        'slug_ar',
        'slug_en',
        'status',
    ];

    protected $appends = ['name'];

    // ------- attributes -------
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    // ------- scopes -------
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }


    // -------- relations --------
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    
}
