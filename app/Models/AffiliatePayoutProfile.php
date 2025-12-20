<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatePayoutProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_code_id',
        'payout_method',
        'payout_details',
        'notes',
    ];

    public function affiliateCode()
    {
        return $this->belongsTo(AffiliateCode::class);
    }
}
