<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';

    protected $fillable = [
        'request_id',
        'supplier_id',
        'price',
        'delivery_time',
        'terms',
        'discount',
        'status',
    ];

    protected $casts = [
        'delivery_time' => 'date',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeMine($query)
    {
        return $query->where('supplier_id', auth('supplier')->user()->supplier_id);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeDeclined($query)
    {
        return $query->where('status', self::STATUS_DECLINED);
    }

    public function getFinalPriceAttribute()
    {
        if ($this->discount) {
            return $this->price - $this->discount;
        }
        return $this->price;
    }

    public function canBeAccepted()
    {
        return $this->status === self::STATUS_PENDING &&
               $this->request->status === Request::STATUS_OPEN;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted()
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isDeclined()
    {
        return $this->status === self::STATUS_DECLINED;
    }
}
