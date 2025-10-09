<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'supplier_id',
        'quantity',
        'price',
        'discount',
        'tax',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the cart that owns the cart item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product for the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the supplier for the cart item.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Calculate and update item total.
     */
    public function calculateTotal(): void
    {
        $itemSubtotal = $this->price * $this->quantity;
        $itemTax = $itemSubtotal * 0.14; // 14% tax - adjust as needed
        $itemDiscount = $this->discount ?? 0;
        
        $this->update([
            'tax' => $itemTax,
            'total' => $itemSubtotal + $itemTax - $itemDiscount,
        ]);
    }
}
