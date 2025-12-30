<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'content',
        'link_url',
        'link_text',
        'open_in_new_tab',
        'position',
        'text_position',
        'text_position_custom',
        'button_position',
        'button_position_custom',
        'text_color',
        'text_background_color',
        'text_background_opacity',
        'text_alignment',
        'button_color',
        'button_text_color',
        'start_at',
        'end_at',
        'status',
        'priority',
        'target_pages',
        'target_categories',
        'target_products',
        'show_on_all_pages',
        'views_count',
        'clicks_count',
        'created_by',
    ];

    protected $appends = ['image', 'mobile_image'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'status' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'show_on_all_pages' => 'boolean',
        'target_pages' => 'array',
        'target_categories' => 'array',
        'target_products' => 'array',
        'text_position_custom' => 'array',
        'button_position_custom' => 'array',
        'priority' => 'integer',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
        'text_background_opacity' => 'integer',
    ];

    /**
     * Get the admin who created this banner
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get banner clicks
     */
    public function clicks()
    {
        return $this->hasMany(BannerClick::class);
    }

    /**
     * Scope: Get active banners
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('status', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
    }

    /**
     * Scope: Get banners by position
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope: Order by priority
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Filter by target pages
     * 
     * @param string|null $pageIdentifier The page identifier (e.g., 'home', 'products', 'products.show')
     *                                     If null, will try to get from current route
     */
    public function scopeForPage($query, $pageIdentifier = null)
    {
        return $query->where(function($q) use ($pageIdentifier) {
            // Show on all pages
            $q->where('show_on_all_pages', true);
            
            // Or match target pages
            if ($pageIdentifier) {
                $q->orWhere(function($subQ) use ($pageIdentifier) {
                    $subQ->where('show_on_all_pages', false)
                         ->whereJsonContains('target_pages', $pageIdentifier);
                });
            }
        });
    }

    /**
     * Check if banner is currently active
     */
    public function isActive(): bool
    {
        if (!$this->status) {
            return false;
        }

        $now = now();
        
        if ($this->start_at && $this->start_at->isFuture()) {
            return false;
        }

        if ($this->end_at && $this->end_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment clicks count
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    /**
     * Get banner image (desktop) from Spatie Media
     */
    public function getImageAttribute()
    {
        $media = $this->getMedia('banner_image')->first();
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get banner mobile image from Spatie Media
     */
    public function getMobileImageAttribute()
    {
        $media = $this->getMedia('banner_mobile_image')->first();
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get all banner images
     */
    public function getImagesAttribute()
    {
        return $this->getMedia('banner_image')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner_image')
            ->singleFile();

        $this->addMediaCollection('banner_mobile_image')
            ->singleFile();
    }
}



