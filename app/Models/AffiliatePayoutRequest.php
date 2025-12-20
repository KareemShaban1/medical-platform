<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class AffiliatePayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_code_id',
        'amount',
        'payout_method',
        'payout_details',
        'notes',
        'status',
        'paid_by_admin_id',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function affiliateCode()
    {
        return $this->belongsTo(AffiliateCode::class);
    }

    public function paidByAdmin()
    {
        return $this->belongsTo(Admin::class, 'paid_by_admin_id');
    }
}
