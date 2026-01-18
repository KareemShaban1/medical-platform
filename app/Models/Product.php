<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, InteractsWithMedia;

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
        'final_price',
        'discount_value',
        'stock',
        'reason',
        'tax',
        'shipping',
        'status',
    ];

    public $appends = ['images', 'first_image', 'name'];

    protected $casts = [
        'price_before' => 'decimal:2',
        'price_after' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    public function getImagesAttribute()
    {
        return $this->getMedia('product_images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    public function getFirstImageAttribute()
    {
        return $this->images[0] ?? 'https://placehold.co/350x263';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->slug_en = Str::slug($product->name_en);
            $product->slug_ar = Str::slug($product->name_ar, '-');
        });
        static::updating(function ($product) {
            $product->slug_en = Str::slug($product->name_en);
            $product->slug_ar = Str::slug($product->name_ar, '-');
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

    public static function calculateFinalPrice(float $basePrice): float
    {
        $fixedFee = (float) config('payment_gateways.paymob.fee_fixed', 3);
        $percentFee = (float) config('payment_gateways.paymob.fee_percent', 5);

        return $basePrice + $fixedFee + ($basePrice * $percentFee / 100);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function approvement()
    {
        return $this->morphOne(ModuleApprovement::class, 'module');
    }


    public function scopeMine($query)
    {
        return $query->where('supplier_id', auth('supplier')->user()->supplier_id);
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
        return $query->whereHas('approvement', function ($query) {
            $query->where('action', 'approved');
        });
    }

    public function scopeHasActiveSupplier($query)
    {
        return $query->whereHas('supplier', function ($q) {
            $q->where('status', 1)->where('is_allowed', 1);
        });
    }

    public function scopeNotApproved($query)
    {
        return $query->whereHas('approvement', function ($query) {
            $query->whereNot('action', 'approved');
        });
    }

}
