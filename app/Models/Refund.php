<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'supplier_id',
        'amount',
        'refund_type',
        'status',
        'reason',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
