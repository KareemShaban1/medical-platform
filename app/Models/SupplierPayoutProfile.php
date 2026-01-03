<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayoutProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'payout_method',
        'payout_details',
        'notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get available payout methods
     */
    public static function payoutMethods(): array
    {
        return [
            'bank_transfer' => __('Bank Transfer'),
            'instapay' => __('InstaPay'),
            'vodafone_cash' => __('Vodafone Cash'),
            'orange_cash' => __('Orange Cash'),
            'etisalat_cash' => __('Etisalat Cash'),
            'we_pay' => __('WE Pay'),
            // 'fawry' => __('Fawry'),
        ];
    }
}
