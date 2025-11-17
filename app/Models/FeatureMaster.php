<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureMaster extends Model
{
    use HasFactory;

    protected $table = 'features_master';

    protected $fillable = [
        'code',
        'name',
        'description',
        'unit',
        'value_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function planFeatures()
    {
        return $this->hasMany(PlanFeature::class, 'feature_id');
    }
}

