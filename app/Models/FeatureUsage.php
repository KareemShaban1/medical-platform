<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'feature_id',
        'feature_code',
        'used_count',
        'limit_count',
        'last_reset_at',
    ];

    protected $casts = [
        'used_count' => 'integer',
        'limit_count' => 'integer',
        'last_reset_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function feature()
    {
        return $this->belongsTo(FeatureMaster::class, 'feature_id');
    }

    public function hasRemainingQuota()
    {
        if ($this->limit_count === null) {
            return true;
        }

        return $this->used_count < $this->limit_count;
    }

    public function getRemainingQuota()
    {
        if ($this->limit_count === null) {
            return null; // Unlimited
        }

        return max(0, $this->limit_count - $this->used_count);
    }
}

