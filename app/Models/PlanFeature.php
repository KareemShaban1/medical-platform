<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'feature_id',
        'is_enabled',
        'value',
        'is_limited',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_limited' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function feature()
    {
        return $this->belongsTo(FeatureMaster::class, 'feature_id');
    }
}

