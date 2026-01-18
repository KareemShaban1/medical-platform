<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalPricing extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'rental_space_id',
        'pricing_type',
        'price',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Pricing types
     */
    public const PRICING_TYPES = [
        'hourly' => 'Hourly',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
    ];

    public function rentalSpace()
    {
        return $this->belongsTo(RentalSpace::class);
    }

    /**
     * Get pricing type label
     */
    public function getPricingTypeLabelAttribute(): string
    {
        return __(self::PRICING_TYPES[$this->pricing_type] ?? ucfirst($this->pricing_type));
    }

    /**
     * Get formatted price with type suffix
     */
    public function getFormattedPriceAttribute(): string
    {
        $suffixes = [
            'hourly' => __('/ hour'),
            'daily' => __('/ day'),
            'weekly' => __('/ week'),
            'monthly' => __('/ month'),
        ];

        $suffix = $suffixes[$this->pricing_type] ?? '';
        return number_format($this->price, 2) . ' ' . __('EGP') . ' ' . $suffix;
    }

    /**
     * Scope for specific pricing type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('pricing_type', $type);
    }
}
