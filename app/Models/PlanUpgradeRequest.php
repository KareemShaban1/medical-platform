<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanUpgradeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requestable_type',
        'requestable_id',
        'current_plan_id',
        'requested_plan_id',
        'status',
        'notes',
        'processed_at',
        'processed_by_type',
        'processed_by_id',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function requestable()
    {
        return $this->morphTo();
    }

    public function processedBy()
    {
        return $this->morphTo();
    }

    public function currentPlan()
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    public function requestedPlan()
    {
        return $this->belongsTo(Plan::class, 'requested_plan_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}

