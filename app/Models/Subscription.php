<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'auto_renew',
        'payment_method',
        'payment_status',
        'payment_gateway',
        'transaction_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
    ];

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function featureUsages()
    {
        return $this->hasMany(FeatureUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere(function ($q2) {
                    $q2->where('status', 'active')
                        ->whereNotNull('end_date')
                        ->where('end_date', '<', now());
                });
        });
    }

    public function isActive()
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->end_date === null) {
            return true;
        }

        $endDate = $this->end_date instanceof Carbon ? $this->end_date : Carbon::parse($this->end_date);
        return $endDate->isFuture() || $endDate->isToday();
    }

    public function isExpired()
    {
        return !$this->isActive();
    }
}

