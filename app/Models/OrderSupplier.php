<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSupplier extends Model
{
    protected $fillable = [
        'order_id', 'supplier_id', 'subtotal', 'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
