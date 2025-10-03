<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierSpecializedCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
    ];

    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_specialized_categories')
                    ->withTimestamps();
    }

    public function requests()
    {
        return $this->belongsToMany(Request::class, 'request_categories')
                    ->withTimestamps();
    }
}
