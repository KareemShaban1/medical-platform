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
        'governorate_id',
        'city_id',
        'area_id',
        'slug',
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

    public function specializedCategories()
    {
        return $this->belongsToMany(SupplierSpecializedCategory::class, 'supplier_category_pivot')
            ->withTimestamps();
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function isVerified()
    {
        return $this->status == 1;
    }
    public function subscription()
    {
        return $this->morphOne(Subscription::class, 'subscribable');
    }

    public function users()
    {
        return $this->supplierUsers();
    }

    // Governorate relation
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    // City relation
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Payout profile relation
    public function payoutProfile()
    {
        return $this->hasOne(SupplierPayoutProfile::class);
    }

    // Payout requests relation
    public function payoutRequests()
    {
        return $this->hasMany(SupplierPayoutRequest::class);
    }

    // Order suppliers relation (orders this supplier is part of)
    public function orderSuppliers()
    {
        return $this->hasMany(OrderSupplier::class);
    }

    /**
     * Get eligible orders for payout (completed/shipped, not already in a payout request)
     */
    public function getEligibleOrdersForPayout()
    {
        return $this->orderSuppliers()
            ->select('order_suppliers.*')
            ->join('orders', 'order_suppliers.order_id', '=', 'orders.id')
            ->where(function ($query) {
                $statuses = ['completed', 'shipped', 'delivered', 'Completed', 'Shipped', 'Delivered'];
                $query->whereIn('order_suppliers.status', $statuses)
                    ->orWhereIn('orders.status', $statuses);
            })
            ->whereDoesntHave('payoutRequests', function ($query) {
                $query->whereIn('status', ['pending', 'approved', 'paid']);
            })
            ->with('order')
            ->get();
    }

    /**
     * Get total eligible amount for payout
     */
    public function getEligiblePayoutAmount(): float
    {
        return (float) $this->getEligibleOrdersForPayout()->sum('subtotal');
    }
}
