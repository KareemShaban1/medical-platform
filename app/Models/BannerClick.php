<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner_id',
        'ip_address',
        'user_agent',
        'referrer',
        'clickable_type',
        'clickable_id',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }

    public function clickable()
    {
        return $this->morphTo();
    }

    /**
     * Scope: Get clicks from authenticated users only
     */
    public function scopeFromUsers($query)
    {
        return $query->whereNotNull('clickable_type')
                    ->whereNotNull('clickable_id');
    }

    /**
     * Scope: Get clicks from guests only
     */
    public function scopeFromGuests($query)
    {
        return $query->where(function($q) {
            $q->whereNull('clickable_type')
              ->orWhere('clickable_type', 'guest');
        })->whereNull('clickable_id');
    }
}






