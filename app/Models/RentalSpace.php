<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalSpace extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'name',
        'location',
        'description',
        'status',
        'listing_type',
        'sale_price',
        'amenities',
        'capacity',
        'area_sqm',
    ];

    protected $casts = [
        'status' => 'boolean',
        'amenities' => 'array',
        'sale_price' => 'decimal:2',
        'area_sqm' => 'decimal:2',
        'capacity' => 'integer',
    ];

    public $appends = ['main_image', 'images', 'primary_price', 'formatted_availability'];

    /**
     * Listing types
     */
    public const LISTING_TYPE_RENT = 'rent';
    public const LISTING_TYPE_SALE = 'sale';

    /**
     * Available amenities list
     */
    public const AMENITIES = [
        'wifi' => 'WiFi',
        'air_conditioning' => 'Air Conditioning',
        'parking' => 'Parking',
        'elevator' => 'Elevator',
        'reception' => 'Reception Area',
        'waiting_room' => 'Waiting Room',
        'storage' => 'Storage',
        'bathroom' => 'Private Bathroom',
        'kitchen' => 'Kitchen/Pantry',
        'security' => '24/7 Security',
        'cctv' => 'CCTV',
        'furnished' => 'Furnished',
        'medical_equipment' => 'Medical Equipment',
        'wheelchair_accessible' => 'Wheelchair Accessible',
    ];

    // scope for current clinic
    public function scopeForCurrentClinic($query)
    {
        return $query->where('clinic_id', auth('clinic')->user()->clinic_id);
    }

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

    /**
     * Scope for rent listings
     */
    public function scopeForRent($query)
    {
        return $query->where('listing_type', self::LISTING_TYPE_RENT);
    }

    /**
     * Scope for sale listings
     */
    public function scopeForSale($query)
    {
        return $query->where('listing_type', self::LISTING_TYPE_SALE);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function approvement()
    {
        return $this->morphOne(ModuleApprovement::class, 'module');
    }

    public function availability()
    {
        return $this->hasOne(RentalAvailability::class);
    }

    public function pricing()
    {
        return $this->hasOne(RentalPricing::class);
    }

    /**
     * Get all pricings (multiple pricing types)
     */
    public function pricings()
    {
        return $this->hasMany(RentalPricing::class);
    }

    public function booking()
    {
        return $this->hasMany(RentalBooking::class);
    }

    /**
     * Get recurring schedules
     */
    public function schedules()
    {
        return $this->hasMany(RentalSchedule::class)->orderByRaw("FIELD(day_of_week, 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')");
    }

    /**
     * Get available schedules
     */
    public function availableSchedules()
    {
        return $this->schedules()->where('is_available', true);
    }

    public function getMainImageAttribute()
    {
        return $this->getMedia('main_image')->first()?->getUrl() ?? null;
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('rental_space_images')->map(function ($media) {
            return $media?->getUrl() ?? null;
        })->toArray();
    }

    /**
     * Get primary price (first/main pricing)
     */
    public function getPrimaryPriceAttribute()
    {
        if ($this->listing_type === self::LISTING_TYPE_SALE) {
            return $this->sale_price;
        }
        return $this->pricing?->price;
    }

    /**
     * Get formatted price with type
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->listing_type === self::LISTING_TYPE_SALE) {
            return $this->sale_price ? number_format($this->sale_price, 2) . ' ' . __('EGP') : __('Price on Request');
        }

        $pricing = $this->pricing;
        if (!$pricing) {
            return __('Price on Request');
        }

        $typeLabels = [
            'hourly' => __('/ hour'),
            'daily' => __('/ day'),
            'weekly' => __('/ week'),
            'monthly' => __('/ month'),
        ];

        $typeLabel = $typeLabels[$pricing->pricing_type] ?? '';
        return number_format($pricing->price, 2) . ' ' . __('EGP') . ' ' . $typeLabel;
    }

    /**
     * Get formatted availability summary
     */
    public function getFormattedAvailabilityAttribute(): string
    {
        $schedules = $this->schedules()->where('is_available', true)->get();

        if ($schedules->isEmpty()) {
            $availability = $this->availability;
            if (!$availability) {
                return __('Contact for availability');
            }
            return ucfirst($availability->type);
        }

        $days = $schedules->pluck('day_of_week')->unique()->map(fn($d) => __(ucfirst($d)));

        if ($days->count() === 7) {
            return __('Available Daily');
        }

        if ($days->count() > 3) {
            return $days->count() . ' ' . __('days/week');
        }

        return $days->implode(', ');
    }

    /**
     * Get listing type label
     */
    public function getListingTypeLabelAttribute(): string
    {
        return $this->listing_type === self::LISTING_TYPE_SALE
            ? __('For Sale')
            : __('For Rent');
    }

    /**
     * Get amenities as labels
     */
    public function getAmenitiesLabelsAttribute(): array
    {
        if (!$this->amenities) {
            return [];
        }

        return collect($this->amenities)
            ->map(fn($amenity) => __(self::AMENITIES[$amenity] ?? ucfirst(str_replace('_', ' ', $amenity))))
            ->toArray();
    }

    /**
     * Check if available on specific day
     */
    public function isAvailableOn(string $day): bool
    {
        return $this->schedules()
            ->where('day_of_week', strtolower($day))
            ->where('is_available', true)
            ->exists();
    }

    /**
     * Get schedule for specific day
     */
    public function getScheduleFor(string $day)
    {
        return $this->schedules()
            ->where('day_of_week', strtolower($day))
            ->first();
    }
}