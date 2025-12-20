<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_code_id',
        'subscription_id',
        'amount',
        'discount_percent',
        'discount_amount',
        'commission_percent',
        'commission_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function affiliateCode()
    {
        return $this->belongsTo(AffiliateCode::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
