<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Product extends Model
{
    use SoftDeletes , HasFactory , HasAttachment;

    const ACTIVE = 1;
    const INACTIVE = 0;

    protected $fillable = [
        'supplier_id',
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'sku',
        'price_before',
        'price_after',
        'discount_value',
        'stock',
        'approved',
        'reason',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->slug_en = Str::slug($product->name_en);
            $product->slug_ar = Str::slug($product->name_ar , '-');
        });
        static::updating(function ($product) {
            $product->slug_en = Str::slug($product->name_en);
            $product->slug_ar = Str::slug($product->name_ar , '-');
        });
    }

    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }


    public function scopeMine($query)
    {
        return $query->where('supplier_id', auth('supplier')->id());
    }


    public function scopeActive($query)
    {
        return $query->where('status', self::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::INACTIVE);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function scopeNotApproved($query)
    {
        return $query->where('approved', false);
    }
}
