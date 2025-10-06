<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'clinic_user_id', 'clinic_id', 'number', 'status', 'total',
        'shipping', 'tax', 'discount', 'payment_method', 'payment_status'
    ];

    public function clinicUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function suppliers()
    {
        return $this->hasMany(OrderSupplier::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
