<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_user_id',
        'clinic_id',
        'subtotal',
        'shipping',
        'tax',
        'discount',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the clinic user that owns the cart.
     */
    public function clinicUser(): BelongsTo
    {
        return $this->belongsTo(ClinicUser::class, 'clinic_user_id');
    }

    /**
     * Get the clinic that owns the cart.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    /**
     * Get the cart items for the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate and update cart totals based on items.
     */
    public function calculateTotals(): void
    {
        $this->items->load(['product', 'supplier']);
        
        $subtotal = $this->items->sum('total');
        $shipping = 0; // Can be calculated based on business logic
        $tax = $subtotal * 0.14; // 14% tax rate - adjust as needed
        $discount = $this->discount ?? 0;
        
        $this->update([
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $subtotal + $shipping + $tax - $discount,
        ]);
    }

    /**
     * Get the total item count in the cart.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
