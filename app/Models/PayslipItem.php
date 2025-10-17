<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayslipItem extends Model
{
    /** @use HasFactory<\Database\Factories\PayslipItemFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'payslip_id',
        'type',
        'notes',
        'amount',
    ];

    public function payslip()
    {
        return $this->belongsTo(Payslip::class);
    }
}
