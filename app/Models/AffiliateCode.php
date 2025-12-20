<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliateable_type',
        'affiliateable_id',
        'code',
        'discount_percent',
        'commission_percent',
        'balance',
        'total_earned',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function affiliateable()
    {
        return $this->morphTo();
    }

    public function transactions()
    {
        return $this->hasMany(AffiliateTransaction::class);
    }

    public function payoutProfile()
    {
        return $this->hasOne(AffiliatePayoutProfile::class);
    }

    public function payoutRequests()
    {
        return $this->hasMany(AffiliatePayoutRequest::class);
    }
}
