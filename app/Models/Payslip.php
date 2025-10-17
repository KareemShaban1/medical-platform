<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payslip extends Model
{
    /** @use HasFactory<\Database\Factories\PayslipFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'clinic_user_id',
        'period_start',
        'period_end',
        'gross_amount',
        'deductions',
        'net_amount',
        'status',
        'paid_at',
    ];

    public function clinicUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }

    public function items()
    {
        return $this->hasMany(PayslipItem::class);
    }
}
