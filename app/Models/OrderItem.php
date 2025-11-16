<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'supplier_id', 'quantity', 'price', 'status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::updated(function (OrderItem $item) {
            if (!$item->wasChanged('status')) {
                Log::info('OrderItem status not changed, skipping order status update.');
                return;
            }
            $order = $item->order()->with('items')->first();
            if (!$order) {
                Log::warning(message: 'Order not found for OrderItem ID: ' . $item->id);
                return;
            }

            $itemStatuses = $order->items->pluck('status');
            if ($itemStatuses->isEmpty()) {
                return;
            }
            Log::info('No item statuses found for Order ID: ' . $order->id);
            $newStatus = $order->status;

            if ($itemStatuses->every(function ($status) {
                return $status === 'cancelled';
            })) {
                $newStatus = 'cancelled';
            } elseif ($itemStatuses->every(function ($status) {
                return $status === 'completed';
            })) {
                $newStatus = 'completed';
            } elseif ($itemStatuses->contains('delivering')) {
                $newStatus = 'delivering';
            } elseif ($itemStatuses->contains('processing')) {
                $newStatus = 'processing';
            } elseif ($itemStatuses->contains('pending')) {
                $newStatus = 'pending';
            }

            if ($newStatus !== $order->status) {
                $order->update(['status' => $newStatus]);
                Log::info('Order ID: ' . $order->id . ' status updated to ' . $newStatus);
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
