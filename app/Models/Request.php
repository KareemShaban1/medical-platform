<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Request extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'clinic_id',
        'description',
        'quantity',
        'preferred_specs',
        'timeline',
        'status',
    ];

    protected $casts = [
        'timeline' => 'date',
    ];

    public $appends = ['attachments'];

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('request_attachments')->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
                'size' => $media->size,
            ];
        })->toArray();
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function categories()
    {
        return $this->belongsToMany(SupplierSpecializedCategory::class, 'request_categories')
                    ->withTimestamps();
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function acceptedOffer()
    {
        return $this->hasOne(Offer::class)->where('status', Offer::STATUS_ACCEPTED);
    }

    public function pendingOffers()
    {
        return $this->hasMany(Offer::class)->where('status', Offer::STATUS_PENDING);
    }

    public function scopeMine($query)
    {
        return $query->where('clinic_id', auth('clinic')->user()->clinic_id);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    public function scopeForSupplierCategories($query, $supplierId)
    {
        return $query->whereHas('categories', function ($q) use ($supplierId) {
            $q->whereHas('suppliers', function ($sq) use ($supplierId) {
                $sq->where('suppliers.id', $supplierId);
            });
        });
    }

    public function canReceiveOffers()
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function hasOfferFromSupplier($supplierId)
    {
        return $this->offers()->where('supplier_id', $supplierId)->exists();
    }
}
